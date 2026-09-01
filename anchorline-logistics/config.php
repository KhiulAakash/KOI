<?php
/**
 * Anchorline Logistics - application bootstrap.
 * Local XAMPP defaults - change DB_* and BASE_URL before deploying anywhere else.
 */

define('ROOT_PATH', __DIR__);
define('BASE_URL', '/anchorline-logistics');

define('DB_HOST', 'localhost');
define('DB_NAME', 'anchorline');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'Anchorline Logistics');
define('SITE_URL', 'http://localhost' . BASE_URL);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => BASE_URL . '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once ROOT_PATH . '/includes/db.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/auth.php';
