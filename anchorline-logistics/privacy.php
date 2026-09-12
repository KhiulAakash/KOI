<?php
require_once __DIR__ . '/config.php';
$page_title = 'Privacy notice';
$meta_description = 'How Anchorline Logistics collects, uses and protects the personal data submitted through this site.';
require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Anchorline Logistics</p>
        <h1>Privacy notice</h1>
        <p>What we collect through this site, why, and how it is protected.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <p class="lede">This is a student project (King's Own Institute, ICT726). No real
           freight is booked and this notice describes exactly what the demonstration
           system does - nothing more.</p>

        <h2 class="mt-4">What we collect</h2>
        <ul>
          <li><strong>Account details</strong> - name, email address and a securely hashed
              password, when you register for an account. You may also optionally add a
              phone number and address in "My account" if you want them on file.</li>
          <li><strong>Password reset requests</strong> - if you use "Forgot your password",
              a single-use reset token is generated. Only its cryptographic hash is stored,
              never the token itself, and it automatically expires after one hour.</li>
          <li><strong>Enquiry details</strong> - name, email, phone number, service of
              interest and your message, when you submit the contact form.</li>
          <li><strong>Session data</strong> - a temporary login session cookie, so you stay
              signed in as you move between pages. It is deleted when you log out or close
              your browser.</li>
        </ul>

        <h2 class="mt-4">Why we collect it</h2>
        <p>Solely to demonstrate the site's functionality: to let you register and log in,
           to let staff respond to enquiries, and to let staff and administrators manage
           shipment records. Nothing is used for advertising, and nothing is sold or shared
           with third parties.</p>

        <h2 class="mt-4">How it is protected</h2>
        <ul>
          <li>Passwords are hashed with PHP's <code>password_hash()</code> (bcrypt) - the
              plain password is never stored or logged.</li>
          <li>All database queries use parameterised (prepared) statements, which stops
              malicious input from being run as SQL.</li>
          <li>Every form that changes data is protected by a per-session CSRF token.</li>
          <li>Access to shipment and enquiry management is restricted by account role
              (admin / member / normal) and enforced on the server for every request, not
              just hidden in the page.</li>
          <li>Session cookies are marked <code>HttpOnly</code> so page scripts cannot read
              them.</li>
        </ul>

        <h2 class="mt-4">What we do not do</h2>
        <ul>
          <li>No third-party analytics, advertising pixels or tracking scripts.</li>
          <li>No data is sold, rented or shared outside this system.</li>
          <li>No automated decisions are made about you.</li>
        </ul>

        <h2 class="mt-4">Your choices</h2>
        <p>If you have an account, you can view and correct your own name, email, phone
           and address at any time in <a href="<?php echo e(BASE_URL); ?>/account.php">My
           account</a>, and change your password there too. Forgotten your password
           entirely? Use <a href="<?php echo e(BASE_URL); ?>/forgot-password.php">Forgot
           your password</a> to reset it yourself, no need to contact anyone. For anything
           else - including deleting your account or your enquiry data - you can reach us
           via the <a href="<?php echo e(BASE_URL); ?>/contact.php">contact page</a>.
           Because this is a teaching environment, the database may be reset at any time
           without notice.</p>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
