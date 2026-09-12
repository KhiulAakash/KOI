<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/dashboard.php');
}

$page_title = 'Reset password';
$meta_description = 'Set a new password for your Anchorline Logistics account.';
$robots = 'noindex, nofollow';

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$errors = [];
$done = false;
// Deliberately not named $user: includes/header.php also sets a $user
// (the logged-in session user, always null on this page) in this same
// top-level scope, and would silently overwrite it after being required.
$resetAccount = null;

if ($token !== '') {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE reset_token_hash = ? AND reset_expires_at > NOW()');
    $stmt->execute([hash('sha256', $token)]);
    $resetAccount = $stmt->fetch() ?: null;
}

if (!$resetAccount) {
    $errors['token'] = 'This reset link is invalid or has expired. Request a new one.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $password = (string) ($_POST['password'] ?? '');
    $confirm  = (string) ($_POST['confirm'] ?? '');

    if (mb_strlen($password) < 8) {
        $errors['password'] = 'Use at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors['confirm'] = 'Passwords do not match.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE users SET password_hash = ?, reset_token_hash = NULL, reset_expires_at = NULL WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $resetAccount['id']]);
        $done = true;
    }
}

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Anchorline Logistics</p>
        <h1>Reset password</h1>
        <p>Choose a new password for your account.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap" style="max-width: 32rem;">
        <?php if ($done): ?>
        <p class="feedback feedback--ok" role="status">Your password has been changed. <a href="<?php echo e(BASE_URL); ?>/login.php">Log in</a> with your new password.</p>
        <?php elseif (!$resetAccount): ?>
        <p class="feedback feedback--error" role="alert"><?php echo field_error($errors, 'token'); ?></p>
        <p class="hint"><a href="<?php echo e(BASE_URL); ?>/forgot-password.php">Request a new reset link</a></p>
        <?php else: ?>
        <p class="feedback feedback--error" id="reset-form-feedback" role="status" tabindex="-1"<?php echo $errors ? '' : ' hidden'; ?>>Check the highlighted fields and try again.</p>

        <form class="form" id="reset-form" method="post" action="<?php echo e(BASE_URL); ?>/reset-password.php" data-validate novalidate>
          <?php echo csrf_field(); ?>
          <input type="hidden" name="token" value="<?php echo e($token); ?>">
          <fieldset>
            <legend>New password</legend>
            <div class="field<?php echo field_invalid($errors, 'password'); ?>">
              <label for="password">New password</label>
              <span class="hint" id="password-hint">At least 8 characters.</span>
              <input type="password" id="password" name="password" autocomplete="new-password"
                     minlength="8" required aria-describedby="password-hint password-error"
                     data-error-tooshort="Use at least 8 characters."
                     aria-invalid="<?php echo field_aria_invalid($errors, 'password'); ?>">
              <span class="error" id="password-error" aria-live="polite"><?php echo field_error($errors, 'password'); ?></span>
            </div>
            <div class="field<?php echo field_invalid($errors, 'confirm'); ?>">
              <label for="confirm">Confirm new password</label>
              <input type="password" id="confirm" name="confirm" autocomplete="new-password"
                     minlength="8" required aria-describedby="confirm-error"
                     aria-invalid="<?php echo field_aria_invalid($errors, 'confirm'); ?>">
              <span class="error" id="confirm-error" aria-live="polite"><?php echo field_error($errors, 'confirm'); ?></span>
            </div>
          </fieldset>
          <button class="btn btn--primary" type="submit">Set new password</button>
        </form>
        <?php endif; ?>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
