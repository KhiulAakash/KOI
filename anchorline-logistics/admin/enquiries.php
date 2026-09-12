<?php
require_once __DIR__ . '/../config.php';
require_role(['admin']);

$page_title = 'Manage enquiries';
$meta_description = 'Admin: review and update the status of customer enquiries.';
$robots = 'noindex, nofollow';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'update_status';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM enquiries WHERE id = ?');
        $stmt->execute([$id]);
        flash_set('ok', 'Enquiry #' . $id . ' deleted.');
        redirect(BASE_URL . '/admin/enquiries.php');
    }

    $status = $_POST['status'] ?? '';
    if (array_key_exists($status, ENQUIRY_STATUS_LABELS)) {
        $stmt = $pdo->prepare('UPDATE enquiries SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        flash_set('ok', 'Enquiry #' . $id . ' marked ' . strtolower(label_for(ENQUIRY_STATUS_LABELS, $status)) . '.');
    }
    redirect(BASE_URL . '/admin/enquiries.php');
}

// Optional search: filters by name, email or message text. Bound LIKE
// parameter, same safe pattern used on the other admin list pages.
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = $pdo->prepare(
        'SELECT * FROM enquiries WHERE full_name LIKE ? OR email LIKE ? OR message LIKE ? ORDER BY created_at DESC'
    );
    $stmt->execute([$like, $like, $like]);
    $enquiries = $stmt->fetchAll();
} else {
    $enquiries = $pdo->query('SELECT * FROM enquiries ORDER BY created_at DESC')->fetchAll();
}

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
        <form class="form-row" method="get" action="<?php echo e(BASE_URL); ?>/admin/enquiries.php" style="align-items: flex-end;">
          <div class="field" style="margin-bottom: 0; flex: 1;">
            <label for="q">Search</label>
            <input type="text" id="q" name="q" value="<?php echo e($search); ?>" placeholder="Name, email or message">
          </div>
          <div class="btn-row" style="margin-bottom: 0.5rem;">
            <button class="btn btn--primary btn--sm" type="submit">Search</button>
            <?php if ($search !== ''): ?>
            <a class="btn btn--ghost btn--sm" href="<?php echo e(BASE_URL); ?>/admin/enquiries.php">Clear</a>
            <?php endif; ?>
          </div>
        </form>
        <div class="table-scroll">
          <table class="manifest">
            <caption><?php echo count($enquiries); ?> enquir<?php echo count($enquiries) === 1 ? 'y' : 'ies'; ?><?php echo $search !== '' ? ' matching "' . e($search) . '"' : ''; ?></caption>
            <thead>
              <tr>
                <th scope="col">Date</th><th scope="col">Name</th><th scope="col">Contact</th>
                <th scope="col">Service</th><th scope="col">Message</th><th scope="col">Status</th><th scope="col">Update</th><th scope="col">Delete</th>
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
                    <input type="hidden" name="action" value="update_status">
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
                <td>
                  <form method="post" action="<?php echo e(BASE_URL); ?>/admin/enquiries.php" onsubmit="return confirm('Delete this enquiry? This cannot be undone.');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo (int) $enq['id']; ?>">
                    <button class="btn btn--danger btn--sm" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$enquiries): ?>
              <tr><td colspan="8">No enquiries<?php echo $search !== '' ? ' match that search.' : ' yet.'; ?></td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
