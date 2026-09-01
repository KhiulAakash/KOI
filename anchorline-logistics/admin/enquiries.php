<?php
require_once __DIR__ . '/../config.php';
require_role(['admin']);

$page_title = 'Manage enquiries';
$meta_description = 'Admin: review and update the status of customer enquiries.';
$robots = 'noindex, nofollow';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (array_key_exists($status, ENQUIRY_STATUS_LABELS)) {
        $stmt = $pdo->prepare('UPDATE enquiries SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        flash_set('ok', 'Enquiry #' . $id . ' marked ' . strtolower(label_for(ENQUIRY_STATUS_LABELS, $status)) . '.');
    }
    redirect(BASE_URL . '/admin/enquiries.php');
}

$enquiries = $pdo->query('SELECT * FROM enquiries ORDER BY created_at DESC')->fetchAll();

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Admin</p>
        <h1>Enquiries</h1>
        <p>Everything submitted through the contact form.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <div class="table-scroll">
          <table class="manifest">
            <caption><?php echo count($enquiries); ?> enquiries</caption>
            <thead>
              <tr>
                <th scope="col">Date</th><th scope="col">Name</th><th scope="col">Contact</th>
                <th scope="col">Service</th><th scope="col">Message</th><th scope="col">Status</th><th scope="col">Update</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($enquiries as $enq): ?>
              <tr>
                <td><?php echo e(date('d M Y', strtotime($enq['created_at']))); ?></td>
                <td><?php echo e($enq['full_name']); ?></td>
                <td><a href="mailto:<?php echo e($enq['email']); ?>"><?php echo e($enq['email']); ?></a><br><?php echo e($enq['phone']); ?></td>
                <td><?php echo e(label_for(SERVICE_LABELS, $enq['service'])); ?></td>
                <td><?php echo e(mb_strimwidth($enq['message'], 0, 80, '…')); ?></td>
                <td><span class="badge badge--<?php echo e($enq['status']); ?>"><?php echo e(label_for(ENQUIRY_STATUS_LABELS, $enq['status'])); ?></span></td>
                <td>
                  <form method="post" action="<?php echo e(BASE_URL); ?>/admin/enquiries.php" class="actions">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int) $enq['id']; ?>">
                    <label class="sr-only" for="status-<?php echo (int) $enq['id']; ?>">Status for enquiry <?php echo (int) $enq['id']; ?></label>
                    <select id="status-<?php echo (int) $enq['id']; ?>" name="status">
                      <?php foreach (ENQUIRY_STATUS_LABELS as $value => $label): ?>
                      <option value="<?php echo e($value); ?>"<?php echo $enq['status'] === $value ? ' selected' : ''; ?>><?php echo e($label); ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button class="btn btn--primary btn--sm" type="submit">Save</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$enquiries): ?>
              <tr><td colspan="7">No enquiries yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
