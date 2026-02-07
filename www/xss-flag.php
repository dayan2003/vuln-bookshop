<?php
// xss-flag.php
// Simple helper endpoint for the XSS lab.
// Returns the flag only when fetched from review.php (basic referer check).
// Place this file in your webroot (e.g. /var/www/html/xss-flag.php)

// NOTE: This is for a controlled lab environment only. Do NOT use this pattern on real sites.

header('Content-Type: text/plain; charset=utf-8');

// soft gate: require the request to come from review.php (basic Referer check).
// This is NOT a security control in real apps; it's just to ensure the flag is revealed
// only when a script executes on review.php in the lab.
$ref = $_SERVER['HTTP_REFERER'] ?? '';
if (strpos($ref, '/review.php') === false) {
    http_response_code(403);
    echo "Access denied: open /review.php and execute your payload there to fetch this.";
    exit;
}

// Try to get flag from config.php first (simple & fast)
$flag = null;
@include __DIR__ . '/includes/config.php'; // suppress warning if missing

// Try to get the XSS flag from the database first
if (isset($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME)) {
    $conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("SELECT flag_value FROM xss_flags WHERE flag_name = ? LIMIT 1");
        if ($stmt) {
            $name = 'stored_xss';
            $stmt->bind_param('s', $name);
            $stmt->execute();
            $stmt->bind_result($fv);
            if ($stmt->fetch()) {
                $flag = $fv;
            }
            $stmt->close();
        }
        $conn->close();
    }
}

// If not in DB, fall back to config.php
if ($flag === null && isset($FLAG_CONFIG) && is_string($FLAG_CONFIG) && $FLAG_CONFIG !== '') {
    $flag = $FLAG_CONFIG;
}

// Final fallback
if ($flag === null) {
    // Put a harmless placeholder so students know something is wrong if it's missing
    $flag = 'FLAG{missing_xss_flag_placeholder}';
}

echo $flag;
