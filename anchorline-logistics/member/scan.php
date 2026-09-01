<?php
require_once __DIR__ . '/../config.php';
require_role(['admin', 'member']);

$page_title = 'Add scan event';
$meta_description = 'Staff: record a scan event against a consignment.';
$robots = 'noindex, nofollow';

$consignmentId = (int) ($_GET['consignment_id'] ?? $_POST['consignment_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM consignments WHERE id = ?');
$stmt->execute([$consignmentId]);
$consignment = $stmt->fetch();

if (!$consignment) {
    flash_set('error', 'Choose a consignment to add a scan to.');
    redirect(BASE_URL . '/dashboard.php');
}

$errors = [];
$data = ['event_text' => '', 'location' => '', 'status' => $consignment['status']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $data['event_text'] = trim($_POST['event_text'] ?? '');
    $data['location']   = trim($_POST['location'] ?? '');
    $data['status']     = trim($_POST['status'] ?? '');

    if ($data['event_text'] === '') $errors['event_text'] = 'Describe what happened, e.g. "Departed hub".';
    if ($data['location'] === '') $errors['location'] = 'Enter a location.';
    if (!array_key_exists($data['status'], CONSIGNMENT_STATUS_LABELS)) $errors['status'] = 'Choose a status.';

    if (!$errors) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO scan_events (consignment_id, event_text, location, created_by) VALUES (?, ?, ?, ?)');
        $stmt->execute([$consignment['id'], $data['event_text'], $data['location'], current_user()['id']]);
        $stmt = $pdo->prepare('UPDATE consignments SET status = ? WHERE id = ?');
        $stmt->execute([$data['status'], $consignment['id']]);
        $pdo->commit();

        flash_set('ok', 'Scan added to ' . $consignment['waybill'] . '.');
        redirect(BASE_URL . '/member/scan.php?consignment_id=' . $consignment['id']);
    }
}

$events = $pdo->prepare('SELECT * FROM scan_events WHERE consignment_id = ? ORDER BY scanned_at DESC');
$events->execute([$consignment['id']]);
$events = $events->fetchAll();

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Staff</p>
        <h1>Add scan &middot; <?php echo e($consignment['waybill']); ?></h1>
        <p><?php echo e($consignment['origin']); ?> &rarr; <?php echo e($consignment['destination']); ?></p>
      </div>
    </section>

    <section class="section">
      <div class="wrap split">
        <div>
          <p class="feedback feedback--error" id="scan-form-feedback" role="status" tabindex="-1"<?php echo $errors ? '' : ' hidden'; ?>>Check the highlighted fields and try again.</p>
          <form class="form" id="scan-form" method="post" action="<?php echo e(BASE_URL); ?>/member/scan.php" data-validate novalidate>
            <?php echo csrf_field(); ?>
            <input type="hidden" name="consignment_id" value="<?php echo (int) $consignment['id']; ?>">
            <fieldset>
              <legend>New scan event</legend>
              <div class="field<?php echo field_invalid($errors, 'event_text'); ?>">
                <label for="event_text">What happened</label>
                <input type="text" id="event_text" name="event_text" placeholder="Departed sortation hub"
                       value="<?php echo e($data['event_text']); ?>" required aria-describedby="event_text-error">
                <span class="error" id="event_text-error"><?php echo field_error($errors, 'event_text'); ?></span>
              </div>
              <div class="field<?php echo field_invalid($errors, 'location'); ?>">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="Chullora, NSW"
                       value="<?php echo e($data['location']); ?>" required aria-describedby="location-error">
                <span class="error" id="location-error"><?php echo field_error($errors, 'location'); ?></span>
              </div>
              <div class="field<?php echo field_invalid($errors, 'status'); ?>">
                <label for="status">Update consignment status to</label>
                <select id="status" name="status" required aria-describedby="status-error">
                  <?php foreach (CONSIGNMENT_STATUS_LABELS as $value => $label): ?>
                  <option value="<?php echo e($value); ?>"<?php echo $data['status'] === $value ? ' selected' : ''; ?>><?php echo e($label); ?></option>
                  <?php endforeach; ?>
                </select>
                <span class="error" id="status-error"><?php echo field_error($errors, 'status'); ?></span>
              </div>
            </fieldset>
            <button class="btn btn--primary" type="submit">Add scan</button>
          </form>
        </div>

        <div>
          <h2 class="h-sub">Scan history</h2>
          <?php if ($events): ?>
          <ol class="rail">
            <?php foreach ($events as $ev): ?>
            <li class="is-done">
              <span class="rail-time"><?php echo e(date('d M, H:i', strtotime($ev['scanned_at']))); ?></span>
              <strong><?php echo e($ev['event_text']); ?>, <?php echo e($ev['location']); ?></strong>
            </li>
            <?php endforeach; ?>
          </ol>
          <?php else: ?>
          <p class="hint">No scans recorded yet.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
