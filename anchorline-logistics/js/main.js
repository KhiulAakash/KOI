/* =============================================================
   Anchorline Logistics - main.js
   Progressive enhancement on top of the PHP/MySQL backend: every
   form here works without JavaScript (a real GET/POST to a PHP
   page that validates and persists it); this file only adds
   instant, accessible inline validation feedback before that
   round-trip. Modules run only if their markup exists on the page.
   ============================================================= */
(function () {
  "use strict";

  /* -----------------------------------------------------------
     1. MOBILE NAVIGATION
     ----------------------------------------------------------- */
  function initNav() {
    var toggle = document.querySelector(".nav-toggle");
    var nav = document.querySelector("#site-nav");
    if (!toggle || !nav) return;

    toggle.addEventListener("click", function () {
      var open = nav.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", String(open));
      toggle.textContent = open ? "Close" : "Menu";
    });

    // Close the menu when a link is chosen or Escape is pressed
    nav.addEventListener("click", function (e) {
      if (e.target.tagName === "A") closeNav();
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") closeNav();
    });

    function closeNav() {
      nav.classList.remove("is-open");
      toggle.setAttribute("aria-expanded", "false");
      toggle.textContent = "Menu";
    }
  }

  /* -----------------------------------------------------------
     2. GALLERY LIGHTBOX
     Thumbnails open a larger version; arrow keys and buttons
     move between images; Escape closes and focus returns to
     the thumbnail that opened it.
     ----------------------------------------------------------- */
  function initLightbox() {
    var gallery = document.querySelector("#gallery");
    var box = document.querySelector("#lightbox");
    if (!gallery || !box) return;

    var buttons = Array.prototype.slice.call(gallery.querySelectorAll("button"));
    var bigImg = box.querySelector("img");
    var caption = box.querySelector("figcaption");
    var counter = box.querySelector("#lightbox-counter");
    var opener = null;
    var index = 0;

    function show(i) {
      index = (i + buttons.length) % buttons.length;
      var thumb = buttons[index].querySelector("img");
      bigImg.src = thumb.dataset.full || thumb.src;
      bigImg.alt = thumb.alt;
      caption.textContent = buttons[index].dataset.caption || thumb.alt;
      counter.textContent = "Image " + (index + 1) + " of " + buttons.length;
    }

    function open(i) {
      opener = buttons[i];
      show(i);
      box.hidden = false;
      document.body.style.overflow = "hidden";
      box.querySelector(".lightbox-close").focus();
    }

    function close() {
      box.hidden = true;
      document.body.style.overflow = "";
      if (opener) opener.focus();
    }

    buttons.forEach(function (btn, i) {
      btn.addEventListener("click", function () { open(i); });
    });

    box.addEventListener("click", function (e) {
      if (e.target === box) close();                      // click the backdrop
      if (e.target.closest(".lightbox-close")) close();
      var nav = e.target.closest(".lightbox-nav");
      if (nav) show(index + (nav.dataset.dir === "next" ? 1 : -1));
    });

    document.addEventListener("keydown", function (e) {
      if (box.hidden) return;
      if (e.key === "Escape") close();
      if (e.key === "ArrowRight") show(index + 1);
      if (e.key === "ArrowLeft") show(index - 1);
    });
  }

  /* -----------------------------------------------------------
     3. SHIPMENT TRACKING
     track.php does the real lookup server-side (it queries
     MySQL and renders the result panel in the page HTML). This
     only adds an accessible client-side format check so a typo
     is caught before the page round-trips to the server.
     ----------------------------------------------------------- */
  function initTracker() {
    var form = document.querySelector("#track-form");
    if (!form) return;

    var input = document.querySelector("#waybill");
    var feedback = document.querySelector("#track-feedback");
    var pattern = /^ANC-\d{4}-(NSW|VIC|QLD|WA|SA|TAS|NT|ACT)$/i;

    form.addEventListener("submit", function (e) {
      var ref = input.value.trim();
      if (!ref) {
        e.preventDefault();
        return fail("Enter a waybill number to track a shipment.");
      }
      if (!pattern.test(ref)) {
        e.preventDefault();
        return fail("Waybill format is ANC-0000-STATE, for example ANC-4471-QLD.");
      }
      if (feedback) feedback.hidden = true;
      // Valid format: let the form submit as a normal GET request to the server.
    });

    function fail(message) {
      if (!feedback) return;
      feedback.hidden = false;
      feedback.className = "feedback feedback--error";
      feedback.textContent = message;
      input.focus();
    }
  }

  /* -----------------------------------------------------------
     4. GENERIC FORM VALIDATION
     HTML5 constraints do the first pass; JavaScript adds inline,
     accessible per-field messages. Any <form data-validate> gets
     wired automatically. Submission is only blocked when a field
     is invalid - a valid form submits for real, to a PHP endpoint
     that re-validates and performs the actual database work.
     Per-field custom copy comes from data-error-* attributes so
     one function serves the contact, register, login and admin
     forms without repeating this logic.
     ----------------------------------------------------------- */
  function initValidatedForms() {
    var forms = Array.prototype.slice.call(document.querySelectorAll("form[data-validate]"));
    forms.forEach(wireForm);

    function wireForm(form) {
      var fields = Array.prototype.slice.call(
        form.querySelectorAll("input, select, textarea")
      );
      var feedback = form.querySelector(".form-feedback") ||
        document.getElementById(form.id + "-feedback");

      function messageFor(field) {
        var v = field.validity;
        if (v.valueMissing) return field.dataset.errorRequired || "This field is required.";
        if (v.typeMismatch) return field.dataset.errorType || "Enter a valid value.";
        if (v.patternMismatch) return field.dataset.errorPattern || "That format is not accepted.";
        if (v.tooShort) return field.dataset.errorTooshort || "Please add a little more detail.";
        if (v.tooLong) return field.dataset.errorToolong || "Please shorten your entry.";
        if (v.rangeUnderflow || v.rangeOverflow) return field.dataset.errorRange || "That value is out of range.";
        return field.validationMessage;
      }

      function setError(field, message) {
        var wrapper = field.closest(".field") || field.closest(".checkbox");
        var slot = document.getElementById(field.id + "-error");
        if (wrapper) wrapper.classList.toggle("is-invalid", Boolean(message));
        field.setAttribute("aria-invalid", message ? "true" : "false");
        if (slot) slot.textContent = message || "";
      }

      fields.forEach(function (field) {
        field.addEventListener("blur", function () {
          setError(field, field.checkValidity() ? "" : messageFor(field));
        });
        field.addEventListener("input", function () {
          if (field.checkValidity()) setError(field, "");
        });
      });

      form.addEventListener("submit", function (e) {
        var firstBad = null;
        fields.forEach(function (field) {
          var ok = field.checkValidity();
          setError(field, ok ? "" : messageFor(field));
          if (!ok && !firstBad) firstBad = field;
        });

        if (firstBad) {
          e.preventDefault();
          if (feedback) {
            feedback.hidden = false;
            feedback.className = "feedback feedback--error";
            feedback.textContent = "Check the highlighted fields and try again.";
          }
          firstBad.focus();
        }
        // Otherwise: let the browser submit the form for real.
      });
    }
  }

  /* -----------------------------------------------------------
     5. FOOTER YEAR
     ----------------------------------------------------------- */
  function initYear() {
    var el = document.querySelector("#year");
    if (el) el.textContent = new Date().getFullYear();
  }

  /* -----------------------------------------------------------
     6. PASSWORD VISIBILITY TOGGLE
     ----------------------------------------------------------- */
  function initPasswordToggles() {
    var toggles = document.querySelectorAll(".password-toggle");
    toggles.forEach(function (btn) {
      var input = document.getElementById(btn.getAttribute("data-target"));
      var label = btn.querySelector(".sr-only");
      if (!input) return;

      btn.addEventListener("click", function () {
        var showing = input.type === "text";
        input.type = showing ? "password" : "text";
        btn.setAttribute("aria-pressed", String(!showing));
        if (label) label.textContent = showing ? "Show password" : "Hide password";
      });
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initNav();
    initLightbox();
    initTracker();
    initValidatedForms();
    initYear();
    initPasswordToggles();
  });
})();
