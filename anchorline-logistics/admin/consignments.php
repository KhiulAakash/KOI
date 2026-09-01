<?php
require_once __DIR__ . '/../config.php';
require_role(['admin']);

$page_title = 'Manage consignments';
$meta_description = 'Admin: create, edit and delete Anchorline consignments.';
$robots = 'noindex, nofollow';

$errors = [];
$editing = null;
$data = [
    'waybill' => '', 'sender_name' => '', 'receiver_name' => '',
    'origin' => '', 'destination' => '', 'service_type' => '', 'status' => 'booked', 'eta' => '',
];

// Load a record into the form for editing.
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM consignments WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editing = $stmt->fetch();
    if ($editing) {
        $data = [
            'waybill' => $editing['waybill'], 'sender_name' => $editing['sender_name'],
            'receiver_name' => $editing['receiver_name'], 'origin' => $editing['origin'],
            'destination' => $editing['destination'], 'service_type' => $editing['service_type'],
            'status' => $editing['status'], 'eta' => $editing['eta'],
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM consignments WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
        flash_set('ok', 'Consignment deleted.');
        redirect(BASE_URL . '/admin/consignments.php');
    }

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $data['waybill']       = strtoupper(trim($_POST['waybill'] ?? ''));
        $data['sender_name']   = trim($_POST['sender_name'] ?? '');
        $data['receiver_name'] = trim($_POST['receiver_name'] ?? '');
        $data['origin']        = trim($_POST['origin'] ?? '');
        $data['destination']   = trim($_POST['destination'] ?? '');
        $data['service_type']  = trim($_POST['service_type'] ?? '');
        $data['status']        = trim($_POST['status'] ?? '');
        $data['eta']           = trim($_POST['eta'] ?? '');

        if (!is_valid_waybill($data['waybill'])) {
            $errors['waybill'] = 'Format is ANC-0000-STATE, for example ANC-4471-QLD.';
        } else {
            $stmt = $pdo->prepare('SELECT id FROM consignments WHERE waybill = ? AND id != ?');
            $stmt->execute([$data['waybill'], $id]);
            if ($stmt->fetch()) {
                $errors['waybill'] = 'That waybill number is already in use.';
            }
        }
        if ($data['sender_name'] === '') $errors['sender_name'] = 'Enter the sender.';
        if ($data['receiver_name'] === '') $errors['receiver_name'] = 'Enter the receiver.';
        if ($data['origin'] === '') $errors['origin'] = 'Enter the origin.';
        if ($data['destination'] === '') $errors['destination'] = 'Enter the destination.';
        if (!array_key_exists($data['service_type'], SERVICE_LABELS)) $errors['service_type'] = 'Choose a service.';
        if (!array_key_exists($data['status'], CONSIGNMENT_STATUS_LABELS)) $errors['status'] = 'Choose a status.';

        if (!$errors) {
            if ($id) {
                $stmt = $pdo->prepare(
                    'UPDATE consignments SET waybill=?, sender_name=?, receiver_name=?, origin=?, destination=?, service_type=?, status=?, eta=? WHERE id=?'
                );
                $stmt->execute([
                    $data['waybill'], $data['sender_name'], $data['receiver_name'], $data['origin'],
                    $data['destination'], $data['service_type'], $data['status'], $data['eta'], $id,
                ]);
                flash_set('ok', 'Consignment ' . $data['waybill'] . ' updated.');
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO consignments (waybill, sender_name, receiver_name, origin, destination, service_type, status, eta, created_by) VALUES (?,?,?,?,?,?,?,?,?)'
                );
                $stmt->execute([
                    $data['waybill'], $data['sender_name'], $data['receiver_name'], $data['origin'],
                    $data['destination'], $data['service_type'], $data['status'], $data['eta'], current_user()['id'],
                ]);
                flash_set('ok', 'Consignment ' . $data['waybill'] . ' created.');
            }
            redirect(BASE_URL . '/admin/consignments.php');
        }
        $editing = ['id' => $id];
    }
}

$consignments = $pdo->query('SELECT * FROM consignments ORDER BY updated_at DESC')->fetchAll();

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Admin</p>
        <h1>Consignments</h1>
        <p>Create, edit and delete shipment records.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <div class="dashboard-card">
          <h2><?php echo $editing ? 'Edit consignment' : 'New consignment'; ?></h2>
          <p class="feedback feedback--error" id="consignment-form-feedback" role="status" tabindex="-1"<?php echo $errors ? '' : ' hidden'; ?>>Check the highlighted fields and save again.</p>
          <form class="form" id="consignment-form" method="post" action="<?php echo e(BASE_URL); ?>/admin/consignments.php" data-validate novalidate>
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?php echo (int) ($editing['id'] ?? 0); ?>">
            <div class="form-row">
              <div class="field<?php echo field_invalid($errors, 'waybill'); ?>">
                <label for="waybill">Waybill</label>
                <input type="text" id="waybill" name="waybill" placeholder="ANC-4471-QLD"
                       value="<?php echo e($data['waybill']); ?>" required
                       data-error-pattern="Format is ANC-0000-STATE, for example ANC-4471-QLD."
                       aria-describedby="waybill-error" aria-invalid="<?php echo field_aria_invalid($errors, 'waybill'); ?>">
                <span class="error" id="waybill-error"><?php echo field_error($errors, 'waybill'); ?></span>
              </div>
              <div class="field<?php echo field_invalid($errors, 'status'); ?>">
                <label for="status">Status</label>
                <select id="status" name="status" required aria-describedby="status-error">
                  <?php foreach (CONSIGNMENT_STATUS_LABELS as $value => $label): ?>
                  <option value="<?php echo e($value); ?>"<?php echo $data['status'] === $value ? ' selected' : ''; ?>><?php echo e($label); ?></option>
                  <?php endforeach; ?>
                </select>
                <span class="error" id="status-error"><?php echo field_error($errors, 'status'); ?></span>
              </div>
            </div>
            <div class="form-row">
              <div class="field<?php echo field_invalid($errors, 'sender_name'); ?>">
                <label for="sender_name">Sender</label>
                <input type="text" id="sender_name" name="sender_name" value="<?php echo e($data['sender_name']); ?>" required aria-describedby="sender_name-error">
                <span class="error" id="sender_name-error"><?php echo field_error($errors, 'sender_name'); ?></span>
              </div>
              <div class="field<?php echo field_invalid($errors, 'receiver_name'); ?>">
                <label for="receiver_name">Receiver</label>
                <input type="text" id="receiver_name" name="receiver_name" value="<?php echo e($data['receiver_name']); ?>" required aria-describedby="receiver_name-error">
                <span class="error" id="receiver_name-error"><?php echo field_error($errors, 'receiver_name'); ?></span>
              </div>
            </div>
            <div class="form-row">
              <div class="field<?php echo field_invalid($errors, 'origin'); ?>">
                <label for="origin">Origin</label>
                <input type="text" id="origin" name="origin" value="<?php echo e($data['origin']); ?>" required aria-describedby="origin-error">
                <span class="error" id="origin-error"><?php echo field_error($errors, 'origin'); ?></span>
              </div>
              <div class="field<?php echo field_invalid($errors, 'destination'); ?>">
                <label for="destination">Destination</label>
                <input type="text" id="destination" name="destination" value="<?php echo e($data['destination']); ?>" required aria-describedby="destination-error">
                <span class="error" id="destination-error"><?php echo field_error($errors, 'destination'); ?></span>
              </div>
            </div>
            <div class="form-row">
              <div class="field<?php echo field_invalid($errors, 'service_type'); ?>">
                <label for="service_type">Service</label>
                <select id="service_type" name="service_type" required aria-describedby="service_type-error">
                  <option value="">Choose a service</option>
                  <?php foreach (SERVICE_LABELS as $value => $label): if ($value === 'other') continue; ?>
                  <option value="<?php echo e($value); ?>"<?php echo $data['service_type'] === $value ? ' selected' : ''; ?>><?php echo e($label); ?></option>
                  <?php endforeach; ?>
                </select>
                <span class="error" id="service_type-error"><?php echo field_error($errors, 'service_type'); ?></span>
              </div>
              <div class="field">
                <label for="eta">ETA (free text)</label>
                <input type="text" id="eta" name="eta" placeholder="19 Jul 2026, 14:00 AEST" value="<?php echo e($data['eta']); ?>">
              </div>
            </div>
            <div class="btn-row">
              <button class="btn btn--primary" type="submit"><?php echo $editing ? 'Save changes' : 'Create consignment'; ?></button>
              <?php if ($editing): ?>
              <a class="btn btn--ghost" href="<?php echo e(BASE_URL); ?>/admin/consignments.php">Cancel edit</a>
              <?php endif; ?>
            </div>
          </form>
        </div>

        <div class="dashboard-card mt-4">
          <h2>All consignments</h2>
          <div class="table-scroll">
            <table class="manifest">
              <caption><?php echo count($consignments); ?> consignments</caption>
              <thead>
                <tr><th scope="col">Waybill</th><th scope="col">Route</th><th scope="col">Service</th><th scope="col">Status</th><th scope="col">Actions</th></tr>
              </thead>
              <tbody>
                <?php foreach ($consignments as $c): ?>
                <tr>
                  <td><code><?php echo e($c['waybill']); ?></code></td>
                  <td><?php echo e($c['origin']); ?> &rarr; <?php echo e($c['destination']); ?></td>
                  <td><?php echo e(label_for(SERVICE_LABELS, $c['service_type'])); ?></td>
                  <td><span class="badge badge--<?php echo e($c['status']); ?>"><?php echo e(label_for(CONSIGNMENT_STATUS_LABELS, $c['status'])); ?></span></td>
                  <td class="actions">
                    <a class="btn btn--primary btn--sm" href="<?php echo e(BASE_URL); ?>/admin/consignments.php?edit=<?php echo (int) $c['id']; ?>">Edit</a>
                    <a class="btn btn--ghost btn--sm" href="<?php echo e(BASE_URL); ?>/member/scan.php?consignment_id=<?php echo (int) $c['id']; ?>" style="border-color: rgba(12,42,47,0.35); color: var(--ink);">Add scan</a>
                    <form method="post" action="<?php echo e(BASE_URL); ?>/admin/consignments.php" onsubmit="return confirm('Delete <?php echo e($c['waybill']); ?>? This cannot be undone.');">
                      <?php echo csrf_field(); ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo (int) $c['id']; ?>">
                      <button class="btn btn--danger btn--sm" type="submit">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
