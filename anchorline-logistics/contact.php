<?php
require_once __DIR__ . '/config.php';

$page_title = 'Contact';
$meta_description = 'Contact the Anchorline operations desk or send freight details for a quote.';

$errors = [];
$data = ['full-name' => '', 'email' => '', 'phone' => '', 'service' => '', 'message' => '', 'consent' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $data['full-name'] = trim($_POST['full-name'] ?? '');
    $data['email']     = trim($_POST['email'] ?? '');
    $data['phone']     = trim($_POST['phone'] ?? '');
    $data['service']   = trim($_POST['service'] ?? '');
    $data['message']   = trim($_POST['message'] ?? '');
    $data['consent']   = isset($_POST['consent']) ? '1' : '';

    if (mb_strlen($data['full-name']) < 2) {
        $errors['full-name'] = 'Enter your full name.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address, for example name@example.com.';
    }
    if (!is_valid_au_phone($data['phone'])) {
        $errors['phone'] = 'Use a 10-digit Australian number, for example 0412 345 678.';
    }
    if (!array_key_exists($data['service'], SERVICE_LABELS)) {
        $errors['service'] = 'Choose a service.';
    }
    if (mb_strlen($data['message']) < 20) {
        $errors['message'] = 'Tell us a little more - at least 20 characters.';
    }
    if ($data['consent'] !== '1') {
        $errors['consent'] = 'You must agree before we can contact you.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO enquiries (full_name, email, phone, service, message, consent, user_id)
             VALUES (?, ?, ?, ?, ?, 1, ?)'
        );
        $stmt->execute([
            $data['full-name'],
            $data['email'],
            $data['phone'],
            $data['service'],
            $data['message'],
            is_logged_in() ? current_user()['id'] : null,
        ]);

        $firstName = explode(' ', $data['full-name'])[0];
        flash_set('ok', 'Thanks ' . $firstName . ', your enquiry is logged. A freight coordinator replies within one business day.');
        redirect(BASE_URL . '/contact.php');
    }
}

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Anchorline Logistics</p>
        <h1>Contact</h1>
        <p>Talk to the operations desk, or send the details of a lane and we will price it.</p>
          <p>Our team is here to help with shipment enquiries, delivery updates and general support.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap split">
        <div>
          <p class="eyebrow">Reach us</p>
          <h2>Operations desk, Port Botany</h2>
          <p>The desk is staffed Monday to Friday, 6am to 8pm AEST. Outside those hours,
             after-hours dispatch picks up the same phone line.</p>
          <table class="manifest mb-3">
            <caption>Direct contacts</caption>
            <tbody>
              <tr><th scope="row">Phone</th><td><a href="tel:+61280000000">(02) 8000 0000</a></td></tr>
              <tr><th scope="row">Dispatch</th><td><a href="mailto:dispatch@anchorline.example">dispatch@anchorline.example</a></td></tr>
              <tr><th scope="row">Quotes</th><td><a href="mailto:quotes@anchorline.example">quotes@anchorline.example</a></td></tr>
              <tr><th scope="row">Address</th><td>14 Foreshore Road, Port Botany NSW 2036</td></tr>
            </tbody>
          </table>
          <h3>Social</h3>
          <ul>
            <li><a href="https://www.linkedin.com/" rel="noopener">LinkedIn</a> &mdash; network updates and roles</li>
            <li><a href="https://www.facebook.com/" rel="noopener">Facebook</a> &mdash; depot news</li>
            <li><a href="https://www.instagram.com/" rel="noopener">Instagram</a> &mdash; the yard, mostly trucks</li>
          </ul>
          <p class="hint">Social links point to the platform home pages: this is a student
             project and the accounts are not real.</p>
        </div>

        <div>
          <p class="feedback feedback--error" id="contact-form-feedback" role="status" tabindex="-1"<?php echo $errors ? '' : ' hidden'; ?>>Check the highlighted fields and send again.</p>

          <form class="form" id="contact-form" method="post" action="<?php echo e(BASE_URL); ?>/contact.php#contact-form" data-validate novalidate>
            <?php echo csrf_field(); ?>
            <fieldset>
              <legend>Your details</legend>
              <div class="field<?php echo field_invalid($errors, 'full-name'); ?>">
                <label for="full-name">Full name</label>
                <input type="text" id="full-name" name="full-name" autocomplete="name"
                       value="<?php echo e($data['full-name']); ?>"
                       minlength="2" required aria-describedby="full-name-error"
                       aria-invalid="<?php echo field_aria_invalid($errors, 'full-name'); ?>">
                <span class="error" id="full-name-error" aria-live="polite"><?php echo field_error($errors, 'full-name'); ?></span>
              </div>
              <div class="form-row">
                <div class="field<?php echo field_invalid($errors, 'email'); ?>">
                  <label for="email">Email</label>
                  <input type="email" id="email" name="email" autocomplete="email"
                         value="<?php echo e($data['email']); ?>"
                         placeholder="name@example.com" required aria-describedby="email-error"
                         data-error-type="Enter a valid email address, for example name@example.com."
                         aria-invalid="<?php echo field_aria_invalid($errors, 'email'); ?>">
                  <span class="error" id="email-error" aria-live="polite"><?php echo field_error($errors, 'email'); ?></span>
                </div>
                <div class="field<?php echo field_invalid($errors, 'phone'); ?>">
                  <label for="phone">Phone</label>
                  <input type="tel" id="phone" name="phone" autocomplete="tel"
                         value="<?php echo e($data['phone']); ?>"
                         placeholder="0412 345 678" pattern="0[\d\s\-]{8,12}"
                         required aria-describedby="phone-hint phone-error"
                         data-error-pattern="Use a 10-digit Australian number, for example 0412 345 678."
                         aria-invalid="<?php echo field_aria_invalid($errors, 'phone'); ?>">
                  <span class="hint" id="phone-hint">Australian mobile or landline, 10 digits.</span>
                  <span class="error" id="phone-error" aria-live="polite"><?php echo field_error($errors, 'phone'); ?></span>
                </div>
              </div>
            </fieldset>

            <fieldset>
              <legend>Your enquiry</legend>
              <div class="field<?php echo field_invalid($errors, 'service'); ?>">
                <label for="service">Service needed</label>
                <select id="service" name="service" required aria-describedby="service-error"
                        aria-invalid="<?php echo field_aria_invalid($errors, 'service'); ?>">
                  <option value="">Choose a service</option>
                  <?php foreach (SERVICE_LABELS as $value => $label): ?>
                  <option value="<?php echo e($value); ?>"<?php echo $data['service'] === $value ? ' selected' : ''; ?>><?php echo e($label); ?></option>
                  <?php endforeach; ?>
                </select>
                <span class="error" id="service-error" aria-live="polite"><?php echo field_error($errors, 'service'); ?></span>
              </div>
              <div class="field<?php echo field_invalid($errors, 'message'); ?>">
                <label for="message">Freight details</label>
                <span class="hint" id="message-hint">Origin, destination, pallet count and
                  when it needs to land.</span>
                <textarea id="message" name="message" minlength="20" required
                          aria-describedby="message-hint message-error"
                          data-error-tooshort="Tell us a little more - at least 20 characters."
                          aria-invalid="<?php echo field_aria_invalid($errors, 'message'); ?>"><?php echo e($data['message']); ?></textarea>
                <span class="error" id="message-error" aria-live="polite"><?php echo field_error($errors, 'message'); ?></span>
              </div>
              <div class="field checkbox<?php echo field_invalid($errors, 'consent'); ?>">
                <input type="checkbox" id="consent" name="consent" required
                       aria-describedby="consent-error"<?php echo $data['consent'] === '1' ? ' checked' : ''; ?>
                       aria-invalid="<?php echo field_aria_invalid($errors, 'consent'); ?>">
                <label for="consent">I agree to Anchorline contacting me about this enquiry.</label>
              </div>
              <span class="error" id="consent-error" aria-live="polite"><?php echo field_error($errors, 'consent'); ?></span>
            </fieldset>

            <button class="btn btn--primary" type="submit">Send enquiry</button>
            <p class="hint mt-2">Your details are stored so a coordinator can reply -
               see the <a href="<?php echo e(BASE_URL); ?>/privacy.php">privacy notice</a> for what we
               do with them.</p>
          </form>
        </div>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
