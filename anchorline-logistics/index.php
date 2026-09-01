<?php
require_once __DIR__ . '/config.php';

$page_title = 'Home';
$meta_description = 'Anchorline Logistics moves palletised, refrigerated and containerised freight across Australia from a Port Botany base.';
$structured_data = json_encode([
    '@context'  => 'https://schema.org',
    '@type'     => 'LocalBusiness',
    'name'      => 'Anchorline Logistics',
    'image'     => SITE_URL . '/img/port-terminal.svg',
    'telephone' => '+61280000000',
    'email'     => 'dispatch@anchorline.example',
    'address'   => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => '14 Foreshore Road',
        'addressLocality' => 'Port Botany',
        'addressRegion'   => 'NSW',
        'postalCode'      => '2036',
        'addressCountry'  => 'AU',
    ],
    'openingHours' => 'Mo-Fr 06:00-20:00',
    'url'          => SITE_URL,
    'description'  => 'Freight forwarding, warehousing, cold chain and international freight from Port Botany, NSW.',
], JSON_UNESCAPED_SLASHES);

require ROOT_PATH . '/includes/header.php';
?>
    <section class="hero">
      <div class="wrap">
        <div>
          <p class="eyebrow">Port Botany, NSW &middot; est. 2016</p>
          <h1>Freight that arrives <em>when we said it would</em>.</h1>
          <p>Anchorline Logistics moves palletised, refrigerated and containerised freight
             across Australia. We handle the forwarding, the warehouse, the paperwork and the
             final doorstep, and we show you exactly where the load is at every step.</p>
          <div class="btn-row">
            <a class="btn btn--primary" href="<?php echo e(BASE_URL); ?>/track.php">Track a shipment</a>
            <a class="btn btn--ghost" href="<?php echo e(BASE_URL); ?>/services.php">See what we move</a>
          </div>
        </div>
        <figure class="hero-figure media-figure">
          <img src="<?php echo e(BASE_URL); ?>/img/port-terminal.svg" width="800" height="600"
               alt="Illustration of gantry cranes lifting shipping containers at a night-time terminal">
        </figure>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <p class="eyebrow">By the numbers</p>
        <dl class="stats">
          <div><dt>Depots</dt><dd>11</dd></div>
          <div><dt>Consignments a week</dt><dd>7,400</dd></div>
          <div><dt>On-time delivery</dt><dd>98.2%</dd></div>
          <div><dt>Warehouse floor</dt><dd>26,000 m&sup2;</dd></div>
        </dl>
      </div>
    </section>

    <section class="section section--tight">
      <div class="wrap">
        <p class="eyebrow">What we move</p>
        <h2>Four services, one control room</h2>
        <p class="lede">Every job runs through the same dispatch desk, so a container that
           lands at Port Botany on Monday can be on a pallet in Dandenong by Wednesday
           without changing providers.</p>
        <div class="grid grid--3 mt-4">
          <article class="card">
            <img src="<?php echo e(BASE_URL); ?>/img/line-haul.svg" width="800" height="600"
                 alt="Line-haul truck travelling a highway at dusk">
            <span class="tag">Road freight</span>
            <h3>Line-haul and palletised</h3>
            <p>Nightly runs between capital cities, with tail-lift and forklift options at
               both ends.</p>
          </article>
          <article class="card">
            <img src="<?php echo e(BASE_URL); ?>/img/warehouse-racking.svg" width="800" height="600"
                 alt="Warehouse racking stacked with palletised freight">
            <span class="tag">Warehousing</span>
            <h3>Storage and 3PL</h3>
            <p>Racked, bonded and bulk storage with pick, pack and cycle counting on
               your stock file.</p>
          </article>
          <article class="card">
            <img src="<?php echo e(BASE_URL); ?>/img/cold-chain.svg" width="800" height="600"
                 alt="Temperature controlled freight chamber holding wrapped pallets">
            <span class="tag">Cold chain</span>
            <h3>Chilled and frozen</h3>
            <p>Two to eight degrees or minus eighteen, logged end to end and audited
               against every leg.</p>
          </article>
        </div>
      </div>
    </section>

    <section class="section section--dark">
      <div class="wrap split">
        <div>
          <p class="eyebrow">How a consignment moves</p>
          <h2>Five scans between your dock and theirs</h2>
          <p class="lede">Each scan writes to the same record, which is what you read on the
             tracking page. Nothing is retyped, so nothing goes missing between systems.</p>
          <ol class="rail">
            <li class="is-done"><span class="rail-time">Step 1</span><strong>Booking accepted</strong>
              Your consignment note is raised and a waybill number is issued.</li>
            <li class="is-done"><span class="rail-time">Step 2</span><strong>Collected</strong>
              A driver scans the labels at pickup and the clock starts.</li>
            <li class="is-done"><span class="rail-time">Step 3</span><strong>Hub sortation</strong>
              Freight is sorted at Chullora and loaded to the right line-haul trailer.</li>
            <li class="is-current"><span class="rail-time">Step 4</span><strong>Line-haul</strong>
              The trailer runs overnight to the destination depot.</li>
            <li><span class="rail-time">Step 5</span><strong>Delivered</strong>
              Proof of delivery is captured and attached to the record.</li>
          </ol>
          <div class="btn-row">
            <a class="btn btn--primary" href="<?php echo e(BASE_URL); ?>/track.php">Try the tracker</a>
          </div>
        </div>
        <figure class="media-figure">
          <img src="<?php echo e(BASE_URL); ?>/img/control-room.svg" width="800" height="600"
               alt="Dispatch control room screens showing volumes, trends and a national route map">
          <figcaption>Dispatch, Port Botany. Every leg on one board.</figcaption>
        </figure>
      </div>
    </section>

    <section class="section">
      <div class="wrap grid grid--2">
        <figure class="quote">
          <blockquote>We switched three suppliers for one. The part that actually changed
            our week was being able to answer a customer without ringing anyone.</blockquote>
          <figcaption>Priya Raman &middot; Operations Manager, Kettle &amp; Co Wholesale</figcaption>
        </figure>
        <figure class="quote">
          <blockquote>Our chilled runs used to be a guess. Now every leg has a temperature
            log attached to it, which is what our auditor wanted all along.</blockquote>
          <figcaption>Daniel Whitmore &middot; Supply Chain Lead, Harbourfield Foods</figcaption>
        </figure>
      </div>
    </section>

    <section class="section section--dark section--tight">
      <div class="wrap">
        <h2>Need a lane priced?</h2>
        <p class="lede">Send us the route, the pallet count and the service level. Quotes
           come back the same business day.</p>
        <div class="btn-row">
          <a class="btn btn--primary" href="<?php echo e(BASE_URL); ?>/contact.php">Request a quote</a>
          <a class="btn btn--ghost" href="<?php echo e(BASE_URL); ?>/services.php">Compare service levels</a>
        </div>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
