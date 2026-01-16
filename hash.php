<?php
// ==========================================
// Password Hash Generator for SpaceLink POS
// Usage: http://localhost/spacelink/hash.php?pass=admin123
// ==========================================

header('Content-Type: text/plain');

// Check if password exists in URL
if (!isset($_GET['pass']) || trim($_GET['pass']) === '') {
    echo "Usage: add '?pass=yourpassword' to the URL\n";
    echo "Example: http://localhost/spacelink/hash.php?pass=admin123\n";
    exit;
}

$password = $_GET['pass'];

// Generate secure hash
$hash = password_hash($password, PASSWORD_DEFAULT);

// Output result
echo "Plain Password: $password\n\n";
echo "Hashed Password:\n$hash\n\n";
echo "Copy the hashed password into your database.";
