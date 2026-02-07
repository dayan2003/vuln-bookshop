<?php
// login.php (vulnerable intentionally — only session handling added)

// start session early so we can set session vars before including header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Correct include path (no leading slash; uses the real disk path)
require_once __DIR__ . '/includes/config.php';
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

$msg = '';
$u = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = isset($_POST['username']) ? $_POST['username'] : '';
    $p = isset($_POST['password']) ? $_POST['password'] : '';
    // INSECURE: direct interpolation -> SQLi (intentionally for lab)
    $sql = "SELECT * FROM users WHERE username='$u' AND password='$p' LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();

        // set session so header shows Profile / Logout
        $_SESSION['user_id']   = (int)($row['id'] ?? 0);
        $_SESSION['user_uid']  = $row['uid'] ?? '';
        $_SESSION['is_logged'] = true;
        $_SESSION['role']      = $row['role'] ?? 'user';

        // redirect to home (nav will update because session is set)
        header('Location: /index.php');
        exit;
    } else {
        $msg = "Login failed";
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="container section">
    <div class="login-card">
        <h2>Login</h2>
        <?php if ($msg): ?>
            <div class="error-message">
                <p><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($u, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        
    </div>
</main>

<style>
/* Login-specific styles */
.login-card {
    background: var(--paper);
    border: 1px solid #eee;
    border-radius: var(--radius);
    padding: 2rem;
    max-width: 400px;
    margin: 2rem auto;
    box-shadow: var(--shadow);
}

.login-card h2 {
    margin: 0 0 1.5rem;
    color: var(--green);
    text-align: center;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: var(--muted);
    margin-bottom: 0.5rem;
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: var(--radius);
    font-size: 1rem;
    color: var(--ink);
    background: var(--ivory);
    transition: border-color 0.2s ease;
}

.form-group input:focus {
    outline: none;
    border-color: var(--green);
    box-shadow: 0 0 0 2px rgba(44, 85, 48, 0.1);
}

.btn-primary {
    background: var(--green);
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--radius);
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    transition: background 0.2s ease;
}

.btn-primary:hover {
    background: var(--burg);
}

.error-message {
    background: rgba(212, 175, 55, 0.1);
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: var(--radius);
    padding: 1rem;
    margin-bottom: 1.5rem;
    color: var(--burg);
    text-align: center;
}

.lab-hint {
    margin-top: 1.5rem;
    padding: 1rem;
    background: rgba(212, 175, 55, 0.05);
    border: 1px solid rgba(212, 175, 55, 0.2);
    border-radius: var(--radius);
    text-align: center;
}

.muted-note {
    color: var(--muted);
    font-size: 0.95rem;
}

.muted-note code {
    background: var(--ivory);
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
}

/* Responsive */
@media (max-width: 768px) {
    .login-card {
        padding: 1.5rem;
        margin: 1rem;
    }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>