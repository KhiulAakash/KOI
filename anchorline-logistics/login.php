<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/dashboard.php');
}

$page_title = 'Log in';
$meta_description = 'Log in to your Anchorline Logistics account.';
$robots = 'noindex, follow';

$errors = [];
$data = ['email' => ''];
$redirectTo = $_GET['redirect'] ?? $_POST['redirect'] ?? '';
// Only ever redirect back within this app - never to an attacker-supplied external URL.
if (strpos($redirectTo, BASE_URL) !== 0) {
    $redirectTo = BASE_URL . '/dashboard.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $data['email'] = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        // Deliberately generic: does not reveal whether the email exists.
        $errors['login'] = 'Incorrect email or password.';
    } else {
        log_in_user($user);
        redirect($redirectTo);
    }
}

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Anchorline Logistics</p>
        <h1>Log in</h1>
        <p>Staff and registered customers sign in here.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap" style="max-width: 32rem;">
        <p class="feedback feedback--error" id="login-form-feedback" role="status" tabindex="-1"<?php echo $errors ? '' : ' hidden'; ?>><?php echo field_error($errors, 'login') ?: 'Check the highlighted fields and try again.'; ?></p>

        <form class="form" id="login-form" method="post" action="<?php echo e(BASE_URL); ?>/login.php" data-validate novalidate>
          <?php echo csrf_field(); ?>
          <input type="hidden" name="redirect" value="<?php echo e($redirectTo); ?>">
          <fieldset>
            <legend>Sign in</legend>
            <div class="field">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" autocomplete="email"
                     value="<?php echo e($data['email']); ?>" required aria-describedby="email-error"
                     data-error-type="Enter a valid email address.">
              <span class="error" id="email-error" aria-live="polite"></span>
            </div>
            <div class="field">
              <label for="password">Password</label>
              <input type="password" id="password" name="password" autocomplete="current-password"
                     required aria-describedby="password-error">
              <span class="error" id="password-error" aria-live="polite"></span>
            </div>
          </fieldset>
          <button class="btn btn--primary" type="submit">Log in</button>
          <p class="hint mt-2"><a href="<?php echo e(BASE_URL); ?>/forgot-password.php">Forgot your password?</a></p>
          <p class="hint">No account yet? <a href="<?php echo e(BASE_URL); ?>/register.php">Register</a>.</p>
          <p class="hint">Demo accounts: <code>admin@anchorline.example</code> / <code>Admin@12345</code>
             (admin) and <code>member@anchorline.example</code> / <code>Member@12345</code> (member).</p>
        </form>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
