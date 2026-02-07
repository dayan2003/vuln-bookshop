<?php
// Start session first!
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/config.php';
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// REQUIRE uid parameter (no id fallback)
$uid = isset($_GET['uid']) ? $_GET['uid'] : null;
$row = null;
$userStats = null;
$recentReviews = [];

if ($uid) {
    // Keep simple lookup for lab; escape to avoid SQL syntax errors
    $uid_esc = $conn->real_escape_string($uid);
    $res = $conn->query("SELECT uid, username, secret, role FROM users WHERE uid='$uid_esc' LIMIT 1");
    $row = $res ? $res->fetch_assoc() : null;
}

// Check if viewing own profile
$isOwnProfile = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $uid;
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="container section">
    <?php if ($row): ?>
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar">
                <div class="avatar-circle">
                    <span class="avatar-initial"><?php echo strtoupper(substr($row['username'], 0, 1)); ?></span>
                </div>
            </div>
            <div class="profile-info">
                <h1 class="profile-name"><?php echo htmlspecialchars($row['username']); ?></h1>
                <p class="profile-meta">
                    <?php if (!empty($row['role'])): ?>
                        <span class="profile-badge role-<?php echo strtolower($row['role']); ?>">
                            <?php echo htmlspecialchars(ucfirst($row['role'])); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($isOwnProfile): ?>
                        <span class="profile-badge own-profile">Your Profile</span>
                    <?php endif; ?>
                </p>
            </div>
            <?php if ($isOwnProfile): ?>
                <div class="profile-actions">
                    <a href="edit-profile.php" class="btn btn-primary btn-small">Edit Profile</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- User Information Card -->
        <div class="user-info-card">
            <h2>Account Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Username:</span>
                    <span class="info-value"><?php echo htmlspecialchars($row['username']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Role:</span>
                    <span class="info-value">
                        <span class="role-badge role-<?php echo strtolower($row['role'] ?? 'user'); ?>">
                            <?php echo htmlspecialchars(ucfirst($row['role'] ?? 'User')); ?>
                        </span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Secret Token:</span>
                    <span class="info-value secret-value">
                        <code id="secretValue" class="blurred"><?php echo htmlspecialchars($row['secret']); ?></code>
                        <button type="button" class="btn-reveal" onclick="toggleSecret()">👁️</button>
                    </span>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- User Not Found -->
        <div class="error-state">
            <div class="error-icon">👤</div>
            <h2>User Not Found</h2>
            <p>The user profile you're looking for doesn't exist or has been removed.</p>
            <a href="/" class="btn btn-primary">Back to Home</a>
        </div>
    <?php endif; ?>
</main>

<style>
/* Profile-specific styles */
.profile-header {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 1.5rem;
    align-items: start;
    background: var(--paper);
    border: 1px solid #eee;
    border-radius: var(--radius);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
}

.profile-avatar {
    display: flex;
    justify-content: center;
    align-items: center;
}

.avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--green), var(--burg));
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(44, 85, 48, 0.15);
}

.avatar-initial {
    color: white;
    font-size: 2rem;
    font-weight: 700;
}

.profile-info {
    min-width: 0;
}

.profile-name {
    margin: 0 0 0.5rem;
    color: var(--green);
}

.profile-meta {
    color: var(--muted);
    margin: 0 0 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.profile-badge {
    background: var(--gold);
    color: #1d1d1d;
    padding: 0.25rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.profile-badge.own-profile {
    background: var(--green);
    color: white;
}

.profile-bio {
    color: var(--ink);
    margin: 0 0 1rem;
    line-height: 1.6;
}

.profile-tags {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.tag {
    background: var(--ivory);
    border: 1px solid #e9e6df;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    color: var(--ink);
}

/* Role badges */
.role-badge {
    padding: 0.25rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.role-admin {
    background: var(--burg);
    color: white;
}

.role-user {
    background: var(--green);
    color: white;
}

.role-moderator {
    background: var(--gold);
    color: #1d1d1d;
}

/* User Info Card */
.user-info-card {
    background: var(--paper);
    border: 1px solid #eee;
    border-radius: var(--radius);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
}

.info-grid {
    display: grid;
    gap: 1rem;
    margin-top: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0eee9;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: var(--muted);
    min-width: 120px;
}

.info-value {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.secret-value {
    position: relative;
}

.blurred {
    filter: blur(4px);
    transition: filter 0.3s ease;
    user-select: none;
}

.secret-revealed {
    filter: none;
    user-select: text;
}

.btn-reveal {
    background: none;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.2s ease;
}

.btn-reveal:hover {
    background: var(--ivory);
}

/* Error State */
.error-state {
    text-align: center;
    padding: 3rem 1rem;
}

.error-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-header {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 1rem;
    }
    
    .profile-meta {
        justify-content: center;
    }
    
    .avatar-circle {
        width: 60px;
        height: 60px;
    }
    
    .avatar-initial {
        font-size: 1.5rem;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .info-label {
        min-width: auto;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function toggleSecret() {
    const secretValue = document.getElementById('secretValue');
    const button = document.querySelector('.btn-reveal');
    
    if (secretValue.classList.contains('blurred')) {
        secretValue.classList.remove('blurred');
        secretValue.classList.add('secret-revealed');
        button.textContent = '🙈';
    } else {
        secretValue.classList.remove('secret-revealed');
        secretValue.classList.add('blurred');
        button.textContent = '👁️';
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>