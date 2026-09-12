<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/dashboard.php');
}

$page_title = 'Forgot password';
$meta_description = 'Request a password reset link for your Anchorline Logistics account.';
$robots = 'noindex, nofollow';

$errors = [];
$data = ['email' => ''];
$sent = false;
$resetLink = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $data['email'] = trim($_POST['email'] ?? '');

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();

        // Show the same confirmation whether or not the account exists,
        // so this form can't be used to check which emails are registered.
        $sent = true;

        if ($user) {
            $rawToken  = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);

            // Let MySQL compute its own expiry (NOW() + 1 hour) instead of
            // sending a PHP-calculated timestamp: reset-password.php checks
            // the expiry with MySQL's NOW() too, and if PHP and MySQL ever
            // disagree on what time it is (a common XAMPP-on-Windows
            // mismatch), a PHP-side timestamp could read as already
            // expired the moment it is written.
            $stmt = $pdo->prepare('UPDATE users SET reset_token_hash = ?, reset_expires_at = NOW() + INTERVAL 1 HOUR WHERE id = ?');
            $stmt->execute([$tokenHash, $user['id']]);

            $resetLink = SITE_URL . '/reset-password.php?token=' . $rawToken;
        }
    }
}

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Anchorline Logistics</p>
        <h1>Forgot your password?</h1>
        <p>Enter the email on your account and we will send a link to reset it.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap" style="max-width: 32rem;">
        <?php if ($sent): ?>
        <p class="feedback feedback--ok" role="status">If an account exists for that email, a reset link has been sent.</p>

        <?php if ($resetLink): ?>
        <div class="dashboard-card mt-2">
          <h2>Testing helper</h2>
          <p class="hint">This student project is not connected to a real mail server, so here is
             the link that would normally be emailed to you:</p>
          <p><a href="<?php echo e($resetLink); ?>"><?php echo e($resetLink); ?></a></p>
          <p class="hint">It expires in 1 hour and can only be used once.</p>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <p class="feedback feedback--error" id="forgot-form-feedback" role="status" tabindex="-1"<?php echo $errors ? '' : ' hidden'; ?>>Check the highlighted field and try again.</p>

        <form class="form" id="forgot-form" method="post" action="<?php echo e(BASE_URL); ?>/forgot-password.php" data-validate novalidate>
          <?php echo csrf_field(); ?>
          <fieldset>
            <legend>Reset your password</legend>
            <div class="field<?php echo field_invalid($errors, 'email'); ?>">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" autocomplete="email"
                     value="<?php echo e($data['email']); ?>" required aria-describedby="email-error"
                     data-error-type="Enter a valid email address."
                     aria-invalid="<?php echo field_aria_invalid($errors, 'email'); ?>">
              <span class="error" id="email-error" aria-live="polite"><?php echo field_error($errors, 'email'); ?></span>
            </div>
          </fieldset>
          <button class="btn btn--primary" type="submit">Send reset link</button>
          <p class="hint mt-2"><a href="<?php echo e(BASE_URL); ?>/login.php">Back to log in</a></p>
        </form>
        <?php endif; ?>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
