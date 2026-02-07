<?php
// www/config.php - safe env-driven configuration
// Reads DB creds and public flag from environment variables.
// Keeps no secrets in source. Use docker-compose / host env to set real values.

// Database connection settings (use env vars in your container/host)
$DB_HOST = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'db';
$DB_NAME = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'vulnapp';
$DB_USER = getenv('DB_USER') !== false ? getenv('DB_USER') : 'user';
$DB_PASS = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'pass';

// Public lab flag (safe: set in environment; fallback to harmless default)
$FLAG_CONFIG = getenv('PUBLIC_LAB_FLAG') !== false ? getenv('PUBLIC_LAB_FLAG') : 'FLAG{public__RPIVATR_lab_flag_2025}';

/**
 * Return a mysqli connection or null on failure.
 * Callers should handle null (e.g., show friendly message) rather than allow fatal errors.
 */
function get_db_connection() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    $conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($conn->connect_errno) {
        // Log the error for instructors; do not echo credentials to users.
        error_log("DB connect error: (" . $conn->connect_errno . ") " . $conn->connect_error);
        return null;
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
