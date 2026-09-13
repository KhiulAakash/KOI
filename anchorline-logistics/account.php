<?php
require_once __DIR__ . '/config.php';
require_login();

$page_title = 'My account';
$meta_description = 'Update your Anchorline Logistics account details and password.';
$robots = 'noindex, nofollow';

$userId = current_user()['id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$userId]);
$account = $stmt->fetch();

$profileErrors  = [];
$passwordErrors = [];
$profileData    = [
    'name'    => $account['name'],
    'email'   => $account['email'],
    'phone'   => $account['phone'] ?? '',
    'address' => $account['address'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'profile') {
    verify_csrf();

    $profileData['name']    = trim($_POST['name'] ?? '');
    $profileData['email']   = trim($_POST['email'] ?? '');
    $profileData['phone']   = trim($_POST['phone'] ?? '');
    $profileData['address'] = trim($_POST['address'] ?? '');

    if (mb_strlen($profileData['name']) < 2) {
        $profileErrors['name'] = 'Enter your full name.';
    }
    if (!filter_var($profileData['email'], FILTER_VALIDATE_EMAIL)) {
        $profileErrors['email'] = 'Enter a valid email address.';
    } elseif (!isset($profileErrors['email'])) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $stmt->execute([$profileData['email'], $userId]);
        if ($stmt->fetch()) {
            $profileErrors['email'] = 'Another account already uses that email.';
        }
    }
    // Phone and address are optional - only validate the phone format
    // once something has actually been entered.
    if ($profileData['phone'] !== '' && !is_valid_au_phone($profileData['phone'])) {
        $profileErrors['phone'] = 'Use a 10-digit Australian number, for example 0412 345 678.';
    }
    if (mb_strlen($profileData['address']) > 255) {
        $profileErrors['address'] = 'Keep the address under 255 characters.';
    }

    if (!$profileErrors) {
        $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?');
        $stmt->execute([
            $profileData['name'],
            $profileData['email'],
            $profileData['phone'] !== '' ? $profileData['phone'] : null,
            $profileData['address'] !== '' ? $profileData['address'] : null,
            $userId,
        ]);

        // The session only stores id/name/role - refresh the name so the
        // header greeting updates immediately instead of on next login.
        $_SESSION['user']['name'] = $profileData['name'];

        flash_set('ok', 'Your details have been updated.');
        redirect(BASE_URL . '/account.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'password') {
    verify_csrf();

    $current  = (string) ($_POST['current_password'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $confirm  = (string) ($_POST['confirm'] ?? '');

    if (!password_verify($current, $account['password_hash'])) {
        $passwordErrors['current_password'] = 'Current password is incorrect.';
    }
    if (mb_strlen($password) < 8) {
        $passwordErrors['password'] = 'Use at least 8 characters.';
    }
    if ($password !== $confirm) {
        $passwordErrors['confirm'] = 'Passwords do not match.';
    }

    if (!$passwordErrors) {
        $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $userId]);

        flash_set('ok', 'Your password has been changed.');
        redirect(BASE_URL . '/account.php');
    }
}

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Anchorline Logistics</p>
        <h1>My account</h1>
        <p>Signed in as <span class="role-pill"><?php echo e($account['role']); ?></span></p>
      </div>
    </section>

    <section class="section">
      <div class="wrap" style="max-width: 32rem;">

        <div class="dashboard-card">
          <h2>Your details</h2>
            <p><strong>Account Status:</strong> Active</p>
          <p class="feedback feedback--error" id="profile-form-feedback" role="status" tabindex="-1"<?php echo $profileErrors ? '' : ' hidden'; ?>>Check the highlighted fields and try again.</p>
          <form class="form" id="profile-form" method="post" action="<?php echo e(BASE_URL); ?>/account.php" data-validate novalidate>
            <?php echo csrf_field(); ?>
            <input type="hidden" name="form" value="profile">
            <fieldset>
              <legend class="sr-only">Your details</legend>
              <div class="field<?php echo field_invalid($profileErrors, 'name'); ?>">
                <label for="name">Full name</label>
                <input type="text" id="name" name="name" autocomplete="name" minlength="2"
                       value="<?php echo e($profileData['name']); ?>" required aria-describedby="name-error"
                       aria-invalid="<?php echo field_aria_invalid($profileErrors, 'name'); ?>">
                <span class="error" id="name-error" aria-live="polite"><?php echo field_error($profileErrors, 'name'); ?></span>
              </div>
              <div class="field<?php echo field_invalid($profileErrors, 'email'); ?>">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" autocomplete="email"
                       value="<?php echo e($profileData['email']); ?>" required aria-describedby="email-error"
                       data-error-type="Enter a valid email address."
                       aria-invalid="<?php echo field_aria_invalid($profileErrors, 'email'); ?>">
                <span class="error" id="email-error" aria-live="polite"><?php echo field_error($profileErrors, 'email'); ?></span>
              </div>
              <div class="field<?php echo field_invalid($profileErrors, 'phone'); ?>">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" autocomplete="tel"
                       value="<?php echo e($profileData['phone']); ?>"
                       placeholder="0412 345 678" pattern="0[\d\s\-]{8,12}"
                       aria-describedby="phone-hint phone-error"
                       data-error-pattern="Use a 10-digit Australian number, for example 0412 345 678."
                       aria-invalid="<?php echo field_aria_invalid($profileErrors, 'phone'); ?>">
                <span class="hint" id="phone-hint">Optional. Australian mobile or landline, 10 digits.</span>
                <span class="error" id="phone-error" aria-live="polite"><?php echo field_error($profileErrors, 'phone'); ?></span>
              </div>
              <div class="field<?php echo field_invalid($profileErrors, 'address'); ?>">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" autocomplete="street-address" maxlength="255"
                       value="<?php echo e($profileData['address']); ?>" aria-describedby="address-hint address-error"
                       placeholder="14 Foreshore Road, Port Botany NSW 2036"
                       aria-invalid="<?php echo field_aria_invalid($profileErrors, 'address'); ?>">
                <span class="hint" id="address-hint">Optional.</span>
                <span class="error" id="address-error" aria-live="polite"><?php echo field_error($profileErrors, 'address'); ?></span>
              </div>
            </fieldset>
            <button class="btn btn--primary" type="submit">Save details</button>
          </form>
        </div>

        <div class="dashboard-card">
          <h2>Change password</h2>
          <p class="feedback feedback--error" id="password-form-feedback" role="status" tabindex="-1"<?php echo $passwordErrors ? '' : ' hidden'; ?>>Check the highlighted fields and try again.</p>
          <form class="form" id="password-form" method="post" action="<?php echo e(BASE_URL); ?>/account.php" data-validate novalidate>
            <?php echo csrf_field(); ?>
            <input type="hidden" name="form" value="password">
            <fieldset>
              <legend class="sr-only">Change password</legend>
              <div class="field<?php echo field_invalid($passwordErrors, 'current_password'); ?>">
                <label for="current_password">Current password</label>
                <div class="password-field">
                  <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                         required aria-describedby="current_password-error"
                         aria-invalid="<?php echo field_aria_invalid($passwordErrors, 'current_password'); ?>">
                  <?php echo password_toggle_button('current_password'); ?>
                </div>
                <span class="error" id="current_password-error" aria-live="polite"><?php echo field_error($passwordErrors, 'current_password'); ?></span>
              </div>
              <div class="field<?php echo field_invalid($passwordErrors, 'password'); ?>">
                <label for="password">New password</label>
                <span class="hint" id="password-hint">At least 8 characters.</span>
                <div class="password-field">
                  <input type="password" id="password" name="password" autocomplete="new-password"
                         minlength="8" required aria-describedby="password-hint password-error"
                         data-error-tooshort="Use at least 8 characters."
                         aria-invalid="<?php echo field_aria_invalid($passwordErrors, 'password'); ?>">
                  <?php echo password_toggle_button('password'); ?>
                </div>
                <span class="error" id="password-error" aria-live="polite"><?php echo field_error($passwordErrors, 'password'); ?></span>
              </div>
              <div class="field<?php echo field_invalid($passwordErrors, 'confirm'); ?>">
                <label for="confirm">Confirm new password</label>
                <div class="password-field">
                  <input type="password" id="confirm" name="confirm" autocomplete="new-password"
                         minlength="8" required aria-describedby="confirm-error"
                         aria-invalid="<?php echo field_aria_invalid($passwordErrors, 'confirm'); ?>">
                  <?php echo password_toggle_button('confirm'); ?>
                </div>
                <span class="error" id="confirm-error" aria-live="polite"><?php echo field_error($passwordErrors, 'confirm'); ?></span>
              </div>
            </fieldset>
            <button class="btn btn--primary" type="submit">Change password</button>
          </form>
        </div>

      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
