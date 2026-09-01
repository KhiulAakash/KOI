<?php
require_once __DIR__ . '/config.php';
$page_title = 'About us';
$meta_description = 'How Anchorline Logistics grew from two trucks at Port Botany to a national line-haul network.';
require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Anchorline Logistics</p>
        <h1>About us</h1>
        <p>Two trucks and a shed in 2016. Eleven depots and a national line-haul network today.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap split">
        <div>
          <p class="eyebrow">Who we are</p>
          <h2>A freight company built by people who worked the dock</h2>
          <p>Anchorline started when three dock workers at Port Botany got tired of telling
             customers they would have to ring someone else. The idea was simple: one
             operator for the whole journey, and a record the customer can read without
             asking permission.</p>
          <p>Ten years on the trucks are newer and the network is bigger, but the test has
             not changed. If a customer cannot find out where their freight is in under a
             minute, we have not finished the job.</p>
        </div>
        <figure class="media-figure">
          <img src="<?php echo e(BASE_URL); ?>/img/dock-crew.svg" width="800" height="600"
               alt="Two dock workers in high visibility gear checking a consignment manifest">
          <figcaption>Manifest check before load-out, Port Botany.</figcaption>
        </figure>
      </div>
    </section>

    <section class="section section--dark">
      <div class="wrap">
        <p class="eyebrow">How we got here</p>
        <h2>Ten years, in order</h2>
        <ol class="rail rail--narrow">
          <li class="is-done"><span class="rail-time">2016</span><strong>Two trucks, one shed</strong>
            Local runs between Port Botany and western Sydney warehouses.</li>
          <li class="is-done"><span class="rail-time">2018</span><strong>Chullora hub opens</strong>
            Sortation moves under one roof and overnight line-haul begins.</li>
          <li class="is-done"><span class="rail-time">2020</span><strong>Cold chain licensed</strong>
            Two chilled chambers commissioned for food and pharmaceutical clients.</li>
          <li class="is-done"><span class="rail-time">2023</span><strong>Customs brokerage</strong>
            Licensed in house, so entries are lodged before the vessel berths.</li>
          <li class="is-current"><span class="rail-time">2026</span><strong>Eleven depots</strong>
            Line-haul reaches Perth and Darwin on a fixed weekly schedule.</li>
        </ol>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <p class="eyebrow">What we hold ourselves to</p>
        <h2>Three commitments</h2>
        <div class="grid grid--3 mt-4">
          <article class="card">
            <span class="tag">Commitment 01</span>
            <h3>Tell you first</h3>
            <p>If a load is late we call before you notice. A surprise costs more than a
               delay.</p>
          </article>
          <article class="card">
            <span class="tag">Commitment 02</span>
            <h3>One record, open</h3>
            <p>Every scan, temperature log and proof of delivery sits on the consignment,
               visible to you.</p>
          </article>
          <article class="card">
            <span class="tag">Commitment 03</span>
            <h3>Safe crews</h3>
            <p>Fatigue managed rosters and chain of responsibility training for every
               driver and loader.</p>
          </article>
        </div>
      </div>
    </section>

    <section class="section section--tight">
      <div class="wrap">
        <p class="eyebrow">The operation today</p>
        <dl class="stats">
          <div><dt>People</dt><dd>240</dd></div>
          <div><dt>Prime movers</dt><dd>68</dd></div>
          <div><dt>States served</dt><dd>6</dd></div>
          <div><dt>Years running</dt><dd>10</dd></div>
        </dl>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
