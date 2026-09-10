  </main>

  <footer class="site-footer">
    <div class="wrap">
      <div>
        <section>
          <h2>Anchorline Logistics</h2>
          <p>Freight forwarding, warehousing and last-mile delivery from our Port Botany
             base, with depots in five states.</p>
          <p>14 Foreshore Road, Port Botany NSW 2036</p>
        </section>
        <section>
          <h2>Pages</h2>
          <ul>
            <li><a href="<?php echo e(BASE_URL); ?>/index.php">Home</a></li>
            <li><a href="<?php echo e(BASE_URL); ?>/services.php">Services</a></li>
            <li><a href="<?php echo e(BASE_URL); ?>/track.php">Track a shipment</a></li>
            <li><a href="<?php echo e(BASE_URL); ?>/gallery.php">Gallery</a></li>
            <li><a href="<?php echo e(BASE_URL); ?>/about.php">About us</a></li>
            <li><a href="<?php echo e(BASE_URL); ?>/contact.php">Contact</a></li>
            <li><a href="<?php echo e(BASE_URL); ?>/privacy.php">Privacy notice</a></li>
          </ul>
        </section>
        <section>
          <h2>Operations desk</h2>
          <ul>
            <li><a href="tel:+61280000000">(02) 8000 0000</a></li>
            <li><a href="mailto:dispatch@anchorline.example">dispatch@anchorline.example</a></li>
            <li>Monday to Friday, 6am to 8pm AEST</li>
            <li><a href="<?php echo e(BASE_URL); ?>/contact.php#contact-form">Request a quote</a></li>
          </ul>
        </section>
      </div>
      <p class="footer-base">
        <span>&copy; <span id="year">2026</span> Anchorline Logistics &mdash; student project, not a real company</span>
        <span>ICT726 &middot; King's Own Institute</span>
      </p>
    </div>
  </footer>

  <div class="faqbot">
    <button type="button" id="faqbot-toggle" class="faqbot-toggle" aria-expanded="false" aria-controls="faqbot-panel">
      <span class="faqbot-ring" aria-hidden="true"></span>
      <span class="faqbot-sparkle faqbot-sparkle--1" aria-hidden="true"></span>
      <span class="faqbot-sparkle faqbot-sparkle--2" aria-hidden="true"></span>
      <svg class="faqbot-toggle-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
      <span class="sr-only">Ask a question</span>
      <span class="faqbot-tooltip" aria-hidden="true">Ask a question</span>
    </button>
    <section id="faqbot-panel" class="faqbot-panel" aria-label="Quick answers" hidden>
      <div class="faqbot-panel-head">
        <img src="<?php echo e(BASE_URL); ?>/img/logo.svg" alt="" width="22" height="22">
        <div class="faqbot-panel-title">
          <h2>Anchorline Assistant</h2>
          <p>Usually answers instantly</p>
        </div>
        <button type="button" id="faqbot-close" class="faqbot-close" aria-label="Close quick answers">&times;</button>
      </div>
      <div class="faqbot-body">
        <p class="hint">Ask about tracking, hours, quotes, accounts or services. For
           anything else, use the <a href="<?php echo e(BASE_URL); ?>/contact.php">contact form</a>.</p>
        <div id="faqbot-log" class="faqbot-log" role="log" aria-live="polite"></div>
        <div id="faqbot-chips" class="faqbot-chips">
          <button type="button">Track a shipment</button>
          <button type="button">Opening hours</button>
          <button type="button">Get a quote</button>
          <button type="button">Our services</button>
          <button type="button">Create an account</button>
          <button type="button">Contact us</button>
        </div>
        <form id="faqbot-form" class="faqbot-form">
          <label class="sr-only" for="faqbot-input">Type a question</label>
          <input type="text" id="faqbot-input" name="question" autocomplete="off"
                 placeholder="Type a question&hellip;">
          <button type="submit" class="faqbot-send" aria-label="Send question">&#10148;</button>
        </form>
      </div>
    </section>
  </div>

  <script src="<?php echo e(BASE_URL); ?>/js/main.js"></script>
  <script src="<?php echo e(BASE_URL); ?>/js/chatbot.js"></script>
</body>
</html>
