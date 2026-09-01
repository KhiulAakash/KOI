<?php
require_once __DIR__ . '/config.php';
$page_title = 'Gallery';
$meta_description = 'Photos and video from the Anchorline network: terminal, warehouse, line-haul and last mile.';
require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Anchorline Logistics</p>
        <h1>Gallery</h1>
        <p>The network in pictures, plus a short loop of a consignment crossing the country.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <p class="eyebrow">Motion</p>
        <h2>One consignment, Sydney to Perth</h2>
        <p class="lede">An eight second loop of waybill ANC-4471-QLD moving through the
           national line-haul network. No sound, so it will not surprise anyone.</p>
        <figure class="media-figure mt-4">
          <video controls muted loop playsinline preload="none"
                 poster="<?php echo e(BASE_URL); ?>/media/network-poster.jpg" width="1280" height="720">
            <source src="<?php echo e(BASE_URL); ?>/media/network-loop.mp4" type="video/mp4">
            <p>Your browser cannot play this video.
               <a href="<?php echo e(BASE_URL); ?>/media/network-loop.mp4">Download the MP4 instead</a>.</p>
          </video>
          <figcaption>Animated route map showing depot nodes and the active line-haul leg.</figcaption>
        </figure>
      </div>
    </section>

    <section class="section section--tight">
      <div class="wrap">
        <p class="eyebrow">Stills</p>
        <h2>Select a thumbnail to open it larger</h2>
        <p class="lede">Use the arrow keys to move between images and Escape to close.</p>
        <ul class="gallery mt-4" id="gallery">
          <li>
            <button type="button" data-caption="Port Botany terminal, night shift">
              <img src="<?php echo e(BASE_URL); ?>/img/port-terminal.svg" data-full="<?php echo e(BASE_URL); ?>/img/port-terminal.svg" width="800" height="600" loading="lazy" alt="Gantry cranes lifting containers at a night-time terminal">
              <span class="caption">Port Botany terminal, night shift</span>
            </button>
          </li>
          <li>
            <button type="button" data-caption="Botany distribution centre, aisle 4">
              <img src="<?php echo e(BASE_URL); ?>/img/warehouse-racking.svg" data-full="<?php echo e(BASE_URL); ?>/img/warehouse-racking.svg" width="800" height="600" loading="lazy" alt="Three levels of warehouse racking loaded with pallets">
              <span class="caption">Botany distribution centre, aisle 4</span>
            </button>
          </li>
          <li>
            <button type="button" data-caption="Line-haul B214 departing Chullora">
              <img src="<?php echo e(BASE_URL); ?>/img/line-haul.svg" data-full="<?php echo e(BASE_URL); ?>/img/line-haul.svg" width="800" height="600" loading="lazy" alt="Line-haul truck and trailer on a highway at dusk">
              <span class="caption">Line-haul B214 departing Chullora</span>
            </button>
          </li>
          <li>
            <button type="button" data-caption="Chamber 02 at 4.1 degrees">
              <img src="<?php echo e(BASE_URL); ?>/img/cold-chain.svg" data-full="<?php echo e(BASE_URL); ?>/img/cold-chain.svg" width="800" height="600" loading="lazy" alt="Cold chamber with wrapped pallets and a temperature readout">
              <span class="caption">Chamber 02 at 4.1 degrees</span>
            </button>
          </li>
          <li>
            <button type="button" data-caption="Dispatch desk, Port Botany">
              <img src="<?php echo e(BASE_URL); ?>/img/control-room.svg" data-full="<?php echo e(BASE_URL); ?>/img/control-room.svg" width="800" height="600" loading="lazy" alt="Dispatch screens showing charts and a route map">
              <span class="caption">Dispatch desk, Port Botany</span>
            </button>
          </li>
          <li>
            <button type="button" data-caption="Air uplift, Sydney apron">
              <img src="<?php echo e(BASE_URL); ?>/img/air-freight.svg" data-full="<?php echo e(BASE_URL); ?>/img/air-freight.svg" width="800" height="600" loading="lazy" alt="Freight aircraft loading on the apron at dawn">
              <span class="caption">Air uplift, Sydney apron</span>
            </button>
          </li>
          <li>
            <button type="button" data-caption="Last mile, inner west run">
              <img src="<?php echo e(BASE_URL); ?>/img/last-mile.svg" data-full="<?php echo e(BASE_URL); ?>/img/last-mile.svg" width="800" height="600" loading="lazy" alt="Delivery van on a suburban street">
              <span class="caption">Last mile, inner west run</span>
            </button>
          </li>
          <li>
            <button type="button" data-caption="Manifest check before load-out">
              <img src="<?php echo e(BASE_URL); ?>/img/dock-crew.svg" data-full="<?php echo e(BASE_URL); ?>/img/dock-crew.svg" width="800" height="600" loading="lazy" alt="Two dock workers in high visibility gear checking a manifest">
              <span class="caption">Manifest check before load-out</span>
            </button>
          </li>
        </ul>
      </div>
    </section>

    <div class="lightbox" id="lightbox" role="dialog" aria-modal="true"
         aria-label="Enlarged gallery image" hidden>
      <button class="lightbox-close" type="button">Close &times;</button>
      <button class="lightbox-nav" type="button" data-dir="prev" aria-label="Previous image">&#8249;</button>
      <button class="lightbox-nav" type="button" data-dir="next" aria-label="Next image">&#8250;</button>
      <figure>
        <img src="<?php echo e(BASE_URL); ?>/img/port-terminal.svg" alt="">
        <figcaption></figcaption>
      </figure>
      <p class="sr-only" id="lightbox-counter" aria-live="polite"></p>
    </div>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
