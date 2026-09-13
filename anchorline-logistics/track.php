<?php
require_once __DIR__ . '/config.php';

$page_title = 'Track a shipment';
$meta_description = 'Enter an Anchorline waybill number to see shipment status and scan history.';

$waybill = trim($_GET['waybill'] ?? '');
$error = '';
$consignment = null;
$events = [];

if ($waybill !== '') {
    $waybill = strtoupper($waybill);
    if (!is_valid_waybill($waybill)) {
        $error = 'Waybill format is ANC-0000-STATE, for example ANC-4471-QLD.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM consignments WHERE waybill = ?');
        $stmt->execute([$waybill]);
        $consignment = $stmt->fetch();

        if (!$consignment) {
            $error = 'No shipment found for ' . $waybill . '. Try the sample waybill ANC-4471-QLD.';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM scan_events WHERE consignment_id = ? ORDER BY scanned_at ASC');
            $stmt->execute([$consignment['id']]);
            $events = $stmt->fetchAll();
        }
    }
}

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Anchorline Logistics</p>
        <h1>Track a shipment</h1>
       <p>Enter your waybill number below to check the latest shipment status.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap split">
        <div>
          <form class="form" id="track-form" method="get" action="<?php echo e(BASE_URL); ?>/track.php" novalidate>
            <fieldset>
              <legend>Shipment lookup</legend>
              <div class="field">
                <label for="waybill">Waybill number</label>
                <span class="hint" id="waybill-hint">Format ANC-0000-STATE. It is printed
                  on the top right of your consignment note.</span>
                <input type="text" id="waybill" name="waybill" placeholder="ANC-4471-QLD"
                       value="<?php echo e($waybill); ?>"
                       autocomplete="off" aria-describedby="waybill-hint" required>
              </div>
              <button class="btn btn--primary" type="submit">Track shipment</button>
            </fieldset>
          </form>

          <p class="feedback feedback--error" id="track-feedback" role="alert"<?php echo $error ? '' : ' hidden'; ?>><?php echo e($error); ?></p>

          <h2 class="mt-4 h-sub">Sample waybills</h2>
          <p>Try any of these against the live database:</p>
          <ul>
            <li><code>ANC-4471-QLD</code> &mdash; in transit</li>
            <li><code>ANC-7726-VIC</code> &mdash; delivered, cold chain</li>
            <li><code>ANC-1039-WA</code> &mdash; held at depot</li>
          </ul>
        </div>

        <div>
          <?php if ($consignment): ?>
          <div class="tracker" id="track-result" role="region" aria-live="polite" aria-label="Tracking result">
            <p class="tracker-ref"><?php echo e($consignment['waybill']); ?></p>
            <p>
              <span class="tracker-status<?php echo $consignment['status'] !== 'delivered' ? ' tracker-status--transit' : ''; ?>">
                <?php echo e(label_for(CONSIGNMENT_STATUS_LABELS, $consignment['status'])); ?>
              </span>
            </p>
            <dl class="stats stats--compact">
              <div><dt>Service</dt><dd><?php echo e(label_for(SERVICE_LABELS, $consignment['service_type'])); ?></dd></div>
              <div><dt>Origin</dt><dd><?php echo e($consignment['origin']); ?></dd></div>
              <div><dt>Destination</dt><dd><?php echo e($consignment['destination']); ?></dd></div>
              <div><dt>ETA</dt><dd><?php echo e($consignment['eta'] ?: 'To be confirmed'); ?></dd></div>
            </dl>
            <h3 class="mt-4">Scan history</h3>
            <?php if ($events): ?>
            <ol class="rail">
              <?php foreach ($events as $i => $ev):
                  $isLast = $i === count($events) - 1;
                  $cls = $isLast && $consignment['status'] !== 'delivered' ? 'is-current' : 'is-done';
              ?>
              <li class="<?php echo $cls; ?>">
                <span class="rail-time"><?php echo e(date('d M, H:i', strtotime($ev['scanned_at']))); ?></span>
                <strong><?php echo e($ev['event_text']); ?>, <?php echo e($ev['location']); ?></strong>
              </li>
              <?php endforeach; ?>
            </ol>
            <?php else: ?>
            <p class="hint">No scans recorded yet.</p>
            <?php endif; ?>
          </div>
          <?php endif; ?>
          <figure class="media-figure<?php echo $consignment ? ' mt-4' : ''; ?>">
            <img src="<?php echo e(BASE_URL); ?>/img/control-room.svg" width="800" height="600"
                 alt="Dispatch screens showing shipment volumes and a national route map">
            <figcaption>Scans from every depot write to one record.</figcaption>
          </figure>
        </div>
      </div>
    </section>

    <section class="section section--dark section--tight">
      <div class="wrap">
        <h2>Cannot find a consignment?</h2>
        <p class="lede">Waybill numbers activate about fifteen minutes after pickup. If the
           number still returns nothing after an hour, the operations desk can trace it
           against the driver run sheet.</p>
        <div class="btn-row">
          <a class="btn btn--primary" href="<?php echo e(BASE_URL); ?>/contact.php">Contact the operations desk</a>
        </div>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
