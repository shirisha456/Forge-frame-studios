<?php
/**
 * Forgeframe Studios — Generate bcrypt hash for password
 * Run from CLI: php hash_password.php
 * Or in browser with ?password=YourNewPassword (for lab only; remove in production)
 * Then replace the password in /data/users.txt with admin:$2y$10$... (the hash output)
 */
define('FORGEFRAME', true);
$password = $argv[1] ?? $_GET['password'] ?? null;
if ($password === null) {
    echo "Usage: php hash_password.php <password>\n";
    echo "Or open hash_password.php?password=YourNewPassword in browser (lab only).\n";
    exit(1);
}
echo "Hash for your password:\n";
echo password_hash($password, PASSWORD_BCRYPT) . "\n";
echo "\nReplace the second part of the line in data/users.txt (e.g. admin:PASTE_HASH_HERE)\n";
