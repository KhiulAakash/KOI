<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/dashboard.php');
}

$page_title = 'Register';
$meta_description = 'Create an Anchorline Logistics account to submit enquiries and track your shipments.';
$robots = 'noindex, follow';

$errors = [];
$data = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $data['name']  = trim($_POST['name'] ?? '');
    $data['email'] = trim($_POST['email'] ?? '');
    $password      = (string) ($_POST['password'] ?? '');
    $confirm       = (string) ($_POST['confirm'] ?? '');

    if (mb_strlen($data['name']) < 2) {
        $errors['name'] = 'Enter your full name.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } elseif (!isset($errors['email'])) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'An account with that email already exists.';
        }
    }
    if (mb_strlen($password) < 8) {
        $errors['password'] = 'Use at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors['confirm'] = 'Passwords do not match.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, \'normal\')'
        );
        $stmt->execute([$data['name'], $data['email'], password_hash($password, PASSWORD_DEFAULT)]);

        flash_set('ok', 'Account created. Log in with your new details.');
        redirect(BASE_URL . '/login.php');
    }
}

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Anchorline Logistics</p>
        <h1>Register</h1>
        <p>Create an account to submit enquiries and see your history.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap" style="max-width: 32rem;">
        <p class="feedback feedback--error" id="register-form-feedback" role="status" tabindex="-1"<?php echo $errors ? '' : ' hidden'; ?>>Check the highlighted fields and try again.</p>

        <form class="form" id="register-form" method="post" action="<?php echo e(BASE_URL); ?>/register.php" data-validate novalidate>
          <?php echo csrf_field(); ?>
          <fieldset>
            <legend>Your account</legend>
            <div class="field<?php echo field_invalid($errors, 'name'); ?>">
              <label for="name">Full name</label>
              <input type="text" id="name" name="name" autocomplete="name" minlength="2"
                     value="<?php echo e($data['name']); ?>" required aria-describedby="name-error"
                     aria-invalid="<?php echo field_aria_invalid($errors, 'name'); ?>">
              <span class="error" id="name-error" aria-live="polite"><?php echo field_error($errors, 'name'); ?></span>
            </div>
            <div class="field<?php echo field_invalid($errors, 'email'); ?>">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" autocomplete="email"
                     value="<?php echo e($data['email']); ?>" required aria-describedby="email-error"
                     data-error-type="Enter a valid email address."
                     aria-invalid="<?php echo field_aria_invalid($errors, 'email'); ?>">
              <span class="error" id="email-error" aria-live="polite"><?php echo field_error($errors, 'email'); ?></span>
            </div>
            <div class="field<?php echo field_invalid($errors, 'password'); ?>">
              <label for="password">Password</label>
              <span class="hint" id="password-hint">At least 8 characters.</span>
              <input type="password" id="password" name="password" autocomplete="new-password"
                     minlength="8" required aria-describedby="password-hint password-error"
                     data-error-tooshort="Use at least 8 characters."
                     aria-invalid="<?php echo field_aria_invalid($errors, 'password'); ?>">
              <span class="error" id="password-error" aria-live="polite"><?php echo field_error($errors, 'password'); ?></span>
            </div>
            <div class="field<?php echo field_invalid($errors, 'confirm'); ?>">
              <label for="confirm">Confirm password</label>
              <input type="password" id="confirm" name="confirm" autocomplete="new-password"
                     minlength="8" required aria-describedby="confirm-error"
                     aria-invalid="<?php echo field_aria_invalid($errors, 'confirm'); ?>">
              <span class="error" id="confirm-error" aria-live="polite"><?php echo field_error($errors, 'confirm'); ?></span>
            </div>
          </fieldset>
          <button class="btn btn--primary" type="submit">Create account</button>
          <p class="hint mt-2">Already registered? <a href="<?php echo e(BASE_URL); ?>/login.php">Log in</a>.</p>
        </form>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
