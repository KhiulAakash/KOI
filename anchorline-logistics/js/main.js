/* =============================================================
   Anchorline Logistics - main.js
   All behaviour is client-side only (no server, no PHP).
   Modules run only if their markup exists on the current page.
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
     3. SHIPMENT TRACKING (prototype)
     A static sample dataset stands in for the server-side
     database, which a static site cannot query.
     ----------------------------------------------------------- */
  var SHIPMENTS = {
    "ANC-4471-QLD": {
      status: "In transit",
      state: "transit",
      service: "Road freight - palletised",
      origin: "Port Botany, NSW",
      destination: "Eagle Farm, QLD",
      eta: "19 Jul 2026, 14:00 AEST",
      events: [
        { time: "16 Jul, 08:12", text: "Collected from consignor, Port Botany", done: true },
        { time: "16 Jul, 19:40", text: "Scanned into Chullora sortation hub", done: true },
        { time: "17 Jul, 06:05", text: "Departed Chullora on line-haul B214", done: true },
        { time: "18 Jul, 05:30", text: "Arrived Coffs Harbour changeover depot", current: true },
        { time: "Scheduled", text: "Out for delivery, Eagle Farm" }
      ]
    },
    "ANC-7726-VIC": {
      status: "Delivered",
      state: "done",
      service: "Cold chain - 2 to 8 degrees C",
      origin: "Alexandria, NSW",
      destination: "Dandenong South, VIC",
      eta: "Delivered 15 Jul 2026, 11:22 AEST",
      events: [
        { time: "14 Jul, 07:50", text: "Collected from consignor, Alexandria", done: true },
        { time: "14 Jul, 15:10", text: "Temperature check passed at 4.1 degrees C", done: true },
        { time: "15 Jul, 06:44", text: "Arrived Dandenong South depot", done: true },
        { time: "15 Jul, 11:22", text: "Delivered, signed by R. Okafor", done: true }
      ]
    },
    "ANC-1039-WA": {
      status: "Held at depot",
      state: "transit",
      service: "Sea freight - LCL container",
      origin: "Port Botany, NSW",
      destination: "Fremantle, WA",
      eta: "Awaiting customs release",
      events: [
        { time: "02 Jul, 09:00", text: "Container loaded, vessel MV Corella", done: true },
        { time: "11 Jul, 16:30", text: "Vessel berthed at Fremantle", done: true },
        { time: "12 Jul, 10:15", text: "Held for customs inspection", current: true },
        { time: "Pending", text: "Release and final delivery" }
      ]
    }
  };

  function initTracker() {
    var form = document.querySelector("#track-form");
    if (!form) return;

    var input = document.querySelector("#waybill");
    var feedback = document.querySelector("#track-feedback");
    var panel = document.querySelector("#track-result");
    var pattern = /^ANC-\d{4}-(NSW|VIC|QLD|WA|SA|TAS|NT|ACT)$/i;

    form.addEventListener("submit", function (e) {
      e.preventDefault();                 // static site: nothing is sent anywhere
      var ref = input.value.trim().toUpperCase();
      panel.hidden = true;

      if (!ref) {
        return fail("Enter a waybill number to track a shipment.");
      }
      if (!pattern.test(ref)) {
        return fail("Waybill format is ANC-0000-STATE, for example ANC-4471-QLD.");
      }
      var data = SHIPMENTS[ref];
      if (!data) {
        return fail("No shipment found for " + ref + ". Try the sample waybill ANC-4471-QLD.");
      }
      render(ref, data);
    });

    function fail(message) {
      feedback.hidden = false;
      feedback.className = "feedback feedback--error";
      feedback.textContent = message;
      input.focus();
    }

    function render(ref, data) {
      feedback.hidden = true;
      panel.innerHTML =
        '<p class="tracker-ref">' + ref + '</p>' +
        '<p><span class="tracker-status' +
          (data.state === "transit" ? " tracker-status--transit" : "") + '">' +
          data.status + '</span></p>' +
        '<dl class="stats stats--compact">' +
        '<div><dt>Service</dt><dd>' + data.service + '</dd></div>' +
        '<div><dt>Origin</dt><dd>' + data.origin + '</dd></div>' +
        '<div><dt>Destination</dt><dd>' + data.destination + '</dd></div>' +
        '<div><dt>ETA</dt><dd>' + data.eta + '</dd></div></dl>' +
        '<h3 class="mt-4">Scan history</h3>' +
        '<ol class="rail">' + data.events.map(function (ev) {
          var cls = ev.current ? "is-current" : (ev.done ? "is-done" : "");
          return '<li class="' + cls + '"><span class="rail-time">' + ev.time +
                 '</span><strong>' + ev.text + '</strong></li>';
        }).join("") + '</ol>';
      panel.hidden = false;
    }
  }

  /* -----------------------------------------------------------
     4. CONTACT FORM VALIDATION
     HTML5 constraints do the first pass; JavaScript adds custom
     rules, inline messages and an accessible success state.
     ----------------------------------------------------------- */
  function initContactForm() {
    var form = document.querySelector("#contact-form");
    if (!form) return;

    var feedback = document.querySelector("#form-feedback");
    var fields = Array.prototype.slice.call(
      form.querySelectorAll("input, select, textarea")
    );

    var messages = {
      valueMissing: "This field is required.",
      typeMismatch: "Enter a valid email address, for example name@example.com.",
      patternMismatch: "Use a 10-digit Australian number, for example 0412 345 678.",
      tooShort: "Tell us a little more - at least 20 characters."
    };

    function messageFor(field) {
      var v = field.validity;
      if (v.valueMissing) return messages.valueMissing;
      if (v.typeMismatch) return messages.typeMismatch;
      if (v.patternMismatch) return messages.patternMismatch;
      if (v.tooShort) return messages.tooShort;
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
      e.preventDefault();                 // no back end: nothing is transmitted
      var firstBad = null;

      fields.forEach(function (field) {
        var ok = field.checkValidity();
        setError(field, ok ? "" : messageFor(field));
        if (!ok && !firstBad) firstBad = field;
      });

      feedback.hidden = false;
      if (firstBad) {
        feedback.className = "feedback feedback--error";
        feedback.textContent = "Check the highlighted fields and send again.";
        firstBad.focus();
        return;
      }

      var name = document.getElementById("full-name").value.trim().split(" ")[0];
      feedback.className = "feedback feedback--ok";
      feedback.textContent = "Thanks " + name +
        ", your enquiry is logged. A freight coordinator replies within one business day. " +
        "(Demonstration only - this static site stores and sends nothing.)";
      form.reset();
      fields.forEach(function (f) { setError(f, ""); });
      feedback.focus();
    });
  }

  /* -----------------------------------------------------------
     5. FOOTER YEAR
     ----------------------------------------------------------- */
  function initYear() {
    var el = document.querySelector("#year");
    if (el) el.textContent = new Date().getFullYear();
  }

  document.addEventListener("DOMContentLoaded", function () {
    initNav();
    initLightbox();
    initTracker();
    initContactForm();
    initYear();
  });
})();
