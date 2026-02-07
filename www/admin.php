<?php
// admin.php - public-safe debug endpoint (shows only a lab flag)
// Intended for permanently-public student challenges. DOES NOT read config.php
// or reveal credentials. Configure PUBLIC_LAB_FLAG as an env var if you want a custom flag.

// ---------- CONFIG ----------
$LOG_FILE = __DIR__ . '/admin_access.log';   // access log (writeable by web server)
$DEFAULT_FLAG = 'FLAG{DEBUG_lab_flag_2025}'; // harmless default if env not set
$FLAG = getenv('PUBLIC_LAB_FLAG') !== false ? getenv('PUBLIC_LAB_FLAG') : $DEFAULT_FLAG;

// optional lightweight anti-scrape delay (in seconds)
// set to 0 to disable; small values (1-2) deter casual crawlers but don't block students
$ANTI_SCRAPE_DELAY = 1;

// ---------- log access (non-blocking best-effort) ----------
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$time = date('Y-m-d H:i:s');
$entry = sprintf("[%s] %s | %s\n", $time, $ip, substr($ua,0,200));
@file_put_contents($LOG_FILE, $entry, FILE_APPEND | LOCK_EX);

// small anti-scrape delay (keeps server friendly)
if ($ANTI_SCRAPE_DELAY > 0) {
    // Sleep a tiny bit to slow mass scanners; safe for students.
    usleep((int)($ANTI_SCRAPE_DELAY * 1000000));
}

// ---------- output safe minimal page ----------
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin / Debug (Public Lab)</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    :root{--bg:#f7f6f3;--card:#fff;--ink:#222;--muted:#6b6b6b;--accent:#234624}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);color:var(--ink);display:grid;place-items:center;height:100vh}
    .card{width:min(880px,94%);background:var(--card);padding:22px;border-radius:12px;box-shadow:0 12px 36px rgba(0,0,0,.06);text-align:center}
    h1{margin:0 0 8px;color:var(--accent)}
    p{margin:.4rem 0 1rem;color:var(--muted)}
    .flag{display:inline-block;padding:.9rem 1.2rem;border-radius:10px;background:#0f1720;color:#e6eef6;font-family:monospace;font-weight:700}
    .meta{margin-top:14px;color:#888;font-size:.9rem}
    .log-note{margin-top:10px;color:#b45a5a;font-size:.85rem}
  </style>
</head>
<body>
  <div class="card">
    <h1>Admin / Debug</h1>
    <p>This public debug endpoint intentionally exposes the challenge flag only.</p>
    <div class="flag" role="status" aria-live="polite"><?php echo htmlspecialchars($FLAG, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></div>
    <div class="meta">Find this page to complete the challenge.</div>
    <div class="log-note">Accesses are logged for instructor auditing.</div>
  </div>
</body>
</html>
