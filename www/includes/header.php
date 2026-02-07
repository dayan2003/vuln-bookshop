<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Bookshop VulnLab</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-row">
    <a class="logo" href="/index.php">
      <span class="logo-mark">📚</span>
      <span class="logo-text">DH's VulnBookshop</span>
    </a>

    <form class="search" action="/search.php" method="get">
      <input name="q" type="search" placeholder="Search titles, authors, or users…" aria-label="Search">
      <button aria-label="Search">🔎</button>
    </form>

    <nav class="nav">
      <a href="/index.php">Home</a>
      <div class="nav-dropdown">
        <button type="button" aria-haspopup="true" aria-expanded="false">Categories ▾</button>
        <div class="menu">
          <a href="/search.php?q=Fiction">Fiction</a>
          <a href="/search.php?q=Non-fiction">Non-fiction</a>
          <a href="/search.php?q=Children">Children’s</a>
          <a href="/search.php?q=Local">Local Authors</a>
        </div>
      </div>
      <a href="/review.php">Reviews</a>

<?php if (!empty($_SESSION['is_logged'])): ?>
  <a class="auth-btn" href="/profile.php?uid=<?php echo urlencode($_SESSION['user_uid'] ?? ''); ?>">
    Profile <?php if (!empty($_SESSION['username'])): ?>(<?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>)<?php endif; ?>
  </a>
  <a class="auth-ghost" href="/logout.php">Logout</a>
<?php else: ?>
  <a class="auth-btn" href="/login.php">Login</a>
<?php endif; ?>




      
    </nav>
  </div>
</header>
