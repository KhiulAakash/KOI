/* =============================================================
   Anchorline Logistics - chatbot.js
   Small rule-based FAQ widget: answers a fixed set of common
   questions (tracking, hours, quotes, accounts, services) with
   canned responses, entirely client-side. No server request, no
   database - if a question is not recognised it points the
   visitor to the contact page instead of guessing.

   Progressive enhancement, matching the pattern in main.js: this
   file only wires up behaviour if the widget markup (added in
   includes/footer.php) is present on the page, and does nothing
   otherwise. It never touches auth, the database, or any other
   form on the site.
   ============================================================= */
(function () {
  "use strict";

  // Keyword -> canned answer. Checked in order, first match wins,
  // so more specific phrases are listed before general ones.
  var FAQ = [
    {
      keywords: ["track", "where is", "shipment", "waybill", "consignment"],
      answer: 'Enter your waybill number (format ANC-0000-STATE) on the <a href="track.php">Track a shipment</a> page to see its live status and full scan history.'
    },
    {
      keywords: ["hour", "open", "close", "time"],
      answer: "The operations desk is staffed Monday to Friday, 6am to 8pm AEST. After-hours dispatch picks up outside those times."
    },
    {
      keywords: ["quote", "price", "cost", "how much"],
      answer: 'For a quote, send your lane details through the <a href="contact.php">contact form</a> and a coordinator will reply within one business day.'
    },
    {
      keywords: ["account", "register", "sign up", "signup"],
      answer: 'Create an account on the <a href="register.php">Register</a> page - it lets you submit enquiries and see your history from your dashboard.'
    },
    {
      keywords: ["log in", "login", "password", "sign in"],
      answer: 'Use the <a href="login.php">Log in</a> page with your email and password. Trouble signing in? <a href="contact.php">Contact us</a> and we will help.'
    },
    {
      keywords: ["service", "what do you do", "offer", "freight type"],
      answer: 'We run four service lines: road line-haul, warehousing and 3PL, cold chain, and international sea/air freight with customs brokerage. See <a href="services.php">Services</a> for details.'
    },
    {
      keywords: ["contact", "phone", "call", "email", "speak"],
      answer: 'Call the operations desk on <a href="tel:+61280000000">(02) 8000 0000</a> or email <a href="mailto:dispatch@anchorline.example">dispatch@anchorline.example</a>.'
    },
    {
      keywords: ["privacy", "data", "gdpr"],
      answer: 'See our <a href="privacy.php">privacy notice</a> for exactly what we collect and why.'
    }
  ];

  var FALLBACK = 'I can only help with a few common questions right now - tracking, hours, quotes, accounts and services. For anything else, please <a href="contact.php">contact the operations desk</a> and a person will help.';

  function findAnswer(text) {
    var q = text.toLowerCase();
    for (var i = 0; i < FAQ.length; i++) {
      var entry = FAQ[i];
      for (var j = 0; j < entry.keywords.length; j++) {
        if (q.indexOf(entry.keywords[j]) !== -1) return entry.answer;
      }
    }
    return FALLBACK;
  }

  function escapeHtml(text) {
    var div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  function initFaqBot() {
    var toggle = document.querySelector("#faqbot-toggle");
    var panel = document.querySelector("#faqbot-panel");
    var closeBtn = document.querySelector("#faqbot-close");
    var log = document.querySelector("#faqbot-log");
    var form = document.querySelector("#faqbot-form");
    var input = document.querySelector("#faqbot-input");
    var chips = document.querySelectorAll("#faqbot-chips button");
    if (!toggle || !panel || !form || !input || !log) return; // markup not on this page - do nothing

    function addMessage(who, html) {
      var row = document.createElement("p");
      row.className = "faqbot-msg faqbot-msg--" + who;
      row.innerHTML = (who === "bot" ? "<strong>Anchorline bot:</strong> " : "") + html;
      log.appendChild(row);
      log.scrollTop = log.scrollHeight;
    }

    // The panel starts (and always returns to) `hidden` - it only ever
    // opens in response to a real click on the toggle button or a chip.
    // The `is-open` class is added a frame after `hidden` is cleared so
    // the CSS transition has a starting state to animate from, and
    // removed before `hidden` is restored so the closing fade can play.
    function isOpen() {
      return panel.classList.contains("is-open");
    }

    function openPanel() {
      panel.hidden = false;
      window.requestAnimationFrame(function () {
        panel.classList.add("is-open");
      });
      toggle.setAttribute("aria-expanded", "true");
      input.focus();
    }

    function closePanel() {
      panel.classList.remove("is-open");
      toggle.setAttribute("aria-expanded", "false");
      toggle.focus();
      window.setTimeout(function () {
        panel.hidden = true;
      }, 180);
    }

    toggle.addEventListener("click", function () {
      if (isOpen()) closePanel(); else openPanel();
    });
    if (closeBtn) closeBtn.addEventListener("click", closePanel);
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && isOpen()) closePanel();
    });
    // Click outside the widget closes it too.
    document.addEventListener("click", function (e) {
      if (isOpen() && !panel.contains(e.target) && e.target !== toggle && !toggle.contains(e.target)) {
        closePanel();
      }
    });

    function ask(rawText) {
      var text = rawText.trim();
      if (!text) return;
      addMessage("user", escapeHtml(text));
      addMessage("bot", findAnswer(text));
    }

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      ask(input.value);
      input.value = "";
      input.focus();
    });

    chips.forEach(function (chip) {
      chip.addEventListener("click", function () {
        ask(chip.textContent);
      });
    });
  }

  document.addEventListener("DOMContentLoaded", initFaqBot);
})();
