<?php
/** Session-based authentication and role-based access control. */

function current_user()
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in()
{
    return current_user() !== null;
}

function require_login()
{
    if (!is_logged_in()) {
        redirect(BASE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

/** @param string[] $roles allowed roles, e.g. ['admin'] or ['admin', 'member'] */
function require_role(array $roles)
{
    require_login();
    if (!in_array(current_user()['role'], $roles, true)) {
        http_response_code(403);
        require ROOT_PATH . '/403.php';
        exit;
    }
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf()
{
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(400);
        die('Security check failed: this form expired or was resubmitted. Go back and try again.');
    }
}

function log_in_user(array $userRow)
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'   => (int) $userRow['id'],
        'name' => $userRow['name'],
        'role' => $userRow['role'],
    ];
}

function log_out_user()
{
    $_SESSION = [];
    session_regenerate_id(true);
}
