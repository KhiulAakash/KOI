<?php
require_once __DIR__ . '/config.php';
require_login();

$page_title = 'Dashboard';
$meta_description = 'Your Anchorline Logistics account.';
$robots = 'noindex, nofollow';

$user = current_user();

if ($user['role'] === 'normal') {
    $stmt = $pdo->prepare('SELECT * FROM enquiries WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$user['id']]);
    $myEnquiries = $stmt->fetchAll();
} elseif ($user['role'] === 'member') {
    $consignments = $pdo->query(
        'SELECT * FROM consignments ORDER BY updated_at DESC LIMIT 20'
    )->fetchAll();
} elseif ($user['role'] === 'admin') {
    $counts = [
        'users'        => (int) $pdo->query('SELECT COUNT(*) c FROM users')->fetch()['c'],
        'consignments' => (int) $pdo->query('SELECT COUNT(*) c FROM consignments')->fetch()['c'],
        'enquiries'    => (int) $pdo->query('SELECT COUNT(*) c FROM enquiries WHERE status = \'new\'')->fetch()['c'],
    ];
}

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Anchorline Logistics</p>
        <h1>Hi, <?php echo e($user['name']); ?></h1>
        <p>Signed in as <span class="role-pill"><?php echo e($user['role']); ?></span></p>
      </div>
    </section>

    <section class="section">
      <div class="wrap">

        <?php if ($user['role'] === 'normal'): ?>
        <div class="dashboard-card">
          <h2>Track a shipment</h2>
          <p>Look up any waybill, no account needed.</p>
          <a class="btn btn--primary" href="<?php echo e(BASE_URL); ?>/track.php">Go to tracker</a>
        </div>
        <div class="dashboard-card">
          <h2>Your enquiries</h2>
          <?php if (!$myEnquiries): ?>
          <p class="hint">You have not submitted an enquiry yet. <a href="<?php echo e(BASE_URL); ?>/contact.php">Send one now.</a></p>
          <?php else: ?>
          <div class="table-scroll">
            <table class="manifest">
              <caption>Submitted by you</caption>
              <thead>
                <tr><th scope="col">Date</th><th scope="col">Service</th><th scope="col">Message</th><th scope="col">Status</th></tr>
              </thead>
              <tbody>
                <?php foreach ($myEnquiries as $enq): ?>
                <tr>
                  <td><?php echo e(date('d M Y', strtotime($enq['created_at']))); ?></td>
                  <td><?php echo e(label_for(SERVICE_LABELS, $enq['service'])); ?></td>
                  <td><?php echo e(mb_strimwidth($enq['message'], 0, 60, '…')); ?></td>
                  <td><span class="badge badge--<?php echo e($enq['status']); ?>"><?php echo e(label_for(ENQUIRY_STATUS_LABELS, $enq['status'])); ?></span></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>

        <?php elseif ($user['role'] === 'member'): ?>
        <div class="dashboard-card">
          <h2>Consignments</h2>
          <p class="hint">Add a scan event to move a consignment through its journey.</p>
          <div class="table-scroll mt-2">
            <table class="manifest">
              <caption>Most recently updated</caption>
              <thead>
                <tr><th scope="col">Waybill</th><th scope="col">Route</th><th scope="col">Status</th><th scope="col">Action</th></tr>
              </thead>
              <tbody>
                <?php foreach ($consignments as $c): ?>
                <tr>
                  <td><code><?php echo e($c['waybill']); ?></code></td>
                  <td><?php echo e($c['origin']); ?> &rarr; <?php echo e($c['destination']); ?></td>
                  <td><span class="badge badge--<?php echo e($c['status']); ?>"><?php echo e(label_for(CONSIGNMENT_STATUS_LABELS, $c['status'])); ?></span></td>
                  <td><a class="btn btn--primary btn--sm" href="<?php echo e(BASE_URL); ?>/member/scan.php?consignment_id=<?php echo (int) $c['id']; ?>">Add scan</a></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <?php elseif ($user['role'] === 'admin'): ?>
        <div class="grid grid--3">
          <article class="card">
            <span class="tag">Accounts</span>
            <h3><?php echo (int) $counts['users']; ?> users</h3>
            <p><a href="<?php echo e(BASE_URL); ?>/admin/users.php">Manage users</a></p>
          </article>
          <article class="card">
            <span class="tag">Shipments</span>
            <h3><?php echo (int) $counts['consignments']; ?> consignments</h3>
            <p><a href="<?php echo e(BASE_URL); ?>/admin/consignments.php">Manage consignments</a></p>
          </article>
          <article class="card">
            <span class="tag">Enquiries</span>
            <h3><?php echo (int) $counts['enquiries']; ?> new</h3>
            <p><a href="<?php echo e(BASE_URL); ?>/admin/enquiries.php">Manage enquiries</a></p>
          </article>
        </div>
        <?php endif; ?>

      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
