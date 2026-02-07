<?php
// search.php (fixed: users SELECT returns 2 columns so UNION with 2-col payload works)
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/config.php';

// Local obfuscation secret (keep server-side)
$OBF_SECRET = 'replace_with_a_long_random_string_for_lab_only';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB connection error");
}

$q = isset($_GET['q']) ? $_GET['q'] : '';
$userRows = [];
$bookRows = [];

function obfuscate_name($name, $secret = null, $prefix_len = 3, $suffix_len = 5) {
    $name = (string)$name;
    $clean = preg_replace('/[^A-Za-z0-9]/', '', $name);
    $prefix = strtolower(substr($clean, 0, max(1, $prefix_len)));

    if (empty($secret)) {
        $rand = substr(md5($name), 0, 12);
        $suffix = strtolower(base_convert(substr($rand,0,8), 16, 36));
        $suffix = preg_replace('/[^a-z0-9]/', '', $suffix);
        return htmlspecialchars($prefix . substr($suffix, 0, $suffix_len), ENT_QUOTES, 'UTF-8');
    }

    $h = hash_hmac('sha256', $name, $secret);
    $hex_chunk = substr($h, 8, 12);
    $base36 = strtolower(base_convert($hex_chunk, 16, 36));
    $suffix = preg_replace('/[^a-z0-9]/', '', $base36);

    if (strlen($suffix) < $suffix_len) {
        $hex_more = substr($h, 20, 12);
        $suffix .= strtolower(base_convert($hex_more, 16, 36));
        $suffix = preg_replace('/[^a-z0-9]/', '', $suffix);
    }
    $suffix = substr($suffix, 0, $suffix_len);

    return htmlspecialchars($prefix . $suffix, ENT_QUOTES, 'UTF-8');
}

if ($q !== '') {
    // USERS: return two columns (username, filler) so UNION with 2-column payload works
    $sqlUsers = "SELECT username, '' AS filler FROM users WHERE username LIKE '%$q%'";
    if ($res = $conn->query($sqlUsers)) {
        while($r = $res->fetch_assoc()) $userRows[] = $r;
    }

    // BOOKS (2 columns) - keep vulnerable so UNION must match 2 cols
    $sqlBooks = "SELECT title, author FROM books
                 WHERE title LIKE '%$q%' OR author LIKE '%$q%' OR category LIKE '%$q%'";
    if ($res2 = $conn->query($sqlBooks)) {
        while($r2 = $res2->fetch_assoc()) $bookRows[] = $r2;
    }
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<main class="container section">
  <h2>Search (books & users)</h2>

  <form>
    <input name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search titles, authors, or categories…">
    <button>Search</button>
  </form>

  <h3>Books</h3>
  <ul>
    <?php foreach($bookRows as $b): ?>
      <li><?php echo htmlspecialchars($b['title']) . " — " . htmlspecialchars($b['author']); ?></li>
    <?php endforeach; ?>
  </ul>

  <h3>Users</h3>
  <ul>
    <?php foreach($userRows as $u):
        $alias = obfuscate_name($u['username'], $OBF_SECRET, 3, 5);
    ?>
      <li><?php echo $alias; ?></li>
    <?php endforeach; ?>
  </ul>

  <p class="muted-note">This is a training lab. Inputs are intentionally unsafe for SQLi practice. Sensitive fields (uids/secrets) are not shown in normal search results — use SQLi + UNION to learn how attackers extract hidden columns.</p>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
