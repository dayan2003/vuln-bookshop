<?php
// review.php — isolated stored XSS using session-only comments
// Works with your new comments table (book_title, author, rating, comment)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB connection error");
}

/**
 * Per-user isolated storage: comments a user submits live ONLY in their session.
 * Different browsers (or incognito) won't share session comments.
 */
if (!isset($_SESSION['user_comments'])) {
    $_SESSION['user_comments'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // NOTE: the XSS sink is the 'comment' field (intentionally unsanitized for the lab)
    $user       = $_POST['user']       ?? 'guest';
    $book_title = $_POST['book_title'] ?? 'Untitled';
    $author     = $_POST['author']     ?? 'Unknown';
    $rating_in  = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
    $rating     = max(1, min(5, $rating_in));
    $comment    = $_POST['comment']    ?? ''; // <-- VULNERABLE (stored XSS in session)

    $new_comment = [
        'id'         => count($_SESSION['user_comments']) + 100, // session-local range to avoid clashing with DB ids
        'user'       => $user,
        'book_title' => $book_title,
        'author'     => $author,
        'rating'     => $rating,
        'comment'    => $comment, // XSS payload is stored and later rendered as-is
        'created_at' => date('Y-m-d H:i:s'),
    ];

    array_unshift($_SESSION['user_comments'], $new_comment);
    // Memory safety: keep only the last 10 session comments
    $_SESSION['user_comments'] = array_slice($_SESSION['user_comments'], 0, 10);
}

/**
 * Default/global reviews from DB (same for everyone)
 * Uses your new schema. No writes, read-only.
 */
$default_comments = [];
$sql = "SELECT id, `user`, book_title, author, rating, comment, created_at
        FROM comments
        ORDER BY id ASC
        LIMIT 7";
if ($res = $conn->query($sql)) {
    while ($row = $res->fetch_assoc()) {
        // normalize types just in case
        $row['rating'] = (int)$row['rating'];
        $default_comments[] = $row;
    }
}

// Combine THIS USER'S session comments first, then the shared defaults
$all_comments = array_merge($_SESSION['user_comments'], $default_comments);

?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- Helper JS: only reveals the flag if script executes (via XSS) -->
<script>
async function showXSSFlag() {
  try {
    const res = await fetch('/xss-flag.php', { credentials: 'include' });
    const txt = await res.text();
    if (res.ok) {
      alert('🎉 XSS Flag: ' + txt);
    } else {
      alert('Flag fetch failed: ' + txt);
    }
  } catch (e) {
    alert('Network error fetching flag.');
  }
}
</script>

<main class="container section">
  <h2>📚 Book Reviews</h2>

  <div class="review-form">
    <h3>Write a Review</h3>
    <form method="post">
      <div class="form-row">
        <div class="form-group">
          <label for="user">Your Name:</label>
          <input type="text" id="user" name="user" placeholder="Enter your name">
        </div>
        <div class="form-group">
          <label for="rating">Rating:</label>
          <select id="rating" name="rating" class="rating-select">
            <option value="5">5 ⭐⭐⭐⭐⭐</option>
            <option value="4">4 ⭐⭐⭐⭐</option>
            <option value="3">3 ⭐⭐⭐</option>
            <option value="2">2 ⭐⭐</option>
            <option value="1">1 ⭐</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="book_title">Book Title:</label>
          <input type="text" id="book_title" name="book_title" placeholder="Enter the book title" required>
        </div>
        <div class="form-group">
          <label for="author">Author:</label>
          <input type="text" id="author" name="author" placeholder="Enter the author's name" required>
        </div>
      </div>

      <div class="form-group">
        <label for="comment">Your Review (XSS here):</label>
        <textarea id="comment" name="comment" placeholder="Share your thoughts…" required></textarea>
      </div>

      <button type="submit" class="submit-btn">📝 Post Review</button>
    </form>
  </div>

  <hr>

  <div class="reviews">
    <h3>Recent Reviews</h3>
    <?php
    foreach ($all_comments as $c) {
        $stars = str_repeat('⭐', (int)$c['rating']);
        echo "<div class='review-card'>";
        echo   "<div class='review-header'>";
        echo     "<div class='book-info'>";
        echo       "<h3>" . htmlspecialchars($c['book_title'], ENT_QUOTES, 'UTF-8') . "</h3>";
        echo       "<div class='book-author'>by " . htmlspecialchars($c['author'], ENT_QUOTES, 'UTF-8') . "</div>";
        echo     "</div>";
        echo     "<div class='rating'><span class='stars'>{$stars}</span><span>(" . (int)$c['rating'] . "/5)</span></div>";
        echo   "</div>";
        echo   "<div class='review-meta'>Reviewed by <strong>" . htmlspecialchars($c['user'], ENT_QUOTES, 'UTF-8') . "</strong> on " . date('F j, Y', strtotime($c['created_at'])) . "</div>";
        echo   "<div class='review-content'>";
        // 🔥 INTENTIONALLY VULNERABLE SINK: the comment is rendered raw (stored XSS)
        echo $c['comment'];
        echo   "</div>";
        echo "</div>";
    }
    ?>
  </div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
