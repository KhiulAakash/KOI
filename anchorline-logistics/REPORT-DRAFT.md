# ICT726 Assignment 3 — Development Report

**Student name:** [YOUR NAME] · **Student ID:** [YOUR ID]
**Business:** Anchorline Logistics (freight forwarding, warehousing and last-mile delivery)
**Live site:** [PASTE YOUR GITHUB PAGES URL]
**Repository:** [PASTE YOUR REPO URL]
**Media folder (Google Drive):** [PASTE SHARED LINK — give your tutor access]

> **How to use this draft.** Everything below is factually true of the code you are
> submitting, but the marks for AI Use (3) and Report (4) come from *your* voice and *your*
> decisions. Rewrite the reflection sections in first person, replace every `[YOUR …]`
> placeholder, and paste in your own screenshots. Do not submit it as-is.

---

## 1. Introduction

### 1.1 The business

Anchorline Logistics is a fictional Australian freight operator based at Port Botany, NSW.
It runs four service lines — road line-haul, warehousing and 3PL, cold chain, and
international sea/air freight with in-house customs brokerage. Its customers are wholesalers
and food producers who care about two things: whether the freight will arrive on time, and
whether they can answer their own customer's question without ringing anyone.

That second point drove the whole site. Rather than a brochure, the site is built around the
question a logistics customer actually asks: *where is my freight right now?*

### 1.2 Site structure

| Page | Purpose |
|---|---|
| `index.html` | Positioning, key numbers, service summary, how a consignment moves |
| `services.html` | Detail on each service line plus a service-level transit table |
| `track.html` | Shipment tracking prototype — waybill lookup with validation |
| `gallery.html` | Media page: animated route video + eight-image lightbox gallery |
| `about.html` | Company story, ten-year timeline, operating commitments |
| `contact.html` | Contact details, social links, validated enquiry form |

Navigation is a single shared bar repeated identically on all six pages, with the current
page marked by `aria-current="page"` and a hi-vis underline. Below 720px it collapses into
a button-toggled menu.

### 1.3 Design decisions

**Palette.** Deep petrol `#0c2a2f` and cool concrete `#eff2f0` as the base, with hi-vis
amber `#ffc233` as the single accent and harbour teal `#145661` for links. The amber is
borrowed directly from the industry's own visual language — safety vests, dock line
markings, hazard tape — so it reads as "logistics" rather than decoration. It is used
sparingly: primary buttons, the current-page underline, and the progress markers.

**Typography.** Archivo for display headings (tight, slightly industrial), IBM Plex Sans for
body text, IBM Plex Mono for data: waybill numbers, timestamps, table headers and eyebrow
labels. The mono face carries a meaning — anything set in it is machine-generated data
rather than marketing copy — which makes scan histories and manifest tables read correctly
at a glance.

**Signature element.** A "consignment rail": a dashed vertical line with diamond nodes that
fill amber as steps complete. It appears in three places, and only where the content is
genuinely a sequence — the five-scan journey on the home page, the ten-year company timeline
on About, and the live scan history in the tracker. Reusing one device across three real
sequences ties the site together without adding decoration.

### 1.4 Challenges

*(Rewrite these in your own words — add the ones you actually hit.)*

1. **A static site pretending to have a database.** Tracking obviously needs a server. The
   compromise was a small JavaScript object holding three sample consignments, plus honest
   on-page text saying it is a prototype. Format validation runs first (`ANC-0000-STATE`),
   then the lookup — which means the error messages can distinguish "that is not a waybill
   number" from "that waybill does not exist".
2. **Keeping the lightbox accessible.** The first version opened an overlay and left keyboard
   focus behind on the page. Fixed by moving focus to the close button on open, returning it
   to the triggering thumbnail on close, wiring Escape and arrow keys, and announcing
   "Image *n* of 8" in a visually hidden `aria-live` region.
3. **CSS specificity collisions.** Section padding was being set on both `.section` and
   individual blocks and cancelling out. Resolved by keeping vertical rhythm on `.section`
   only and adding small utility classes (`.mt-4`, `.mb-5`) instead of inline styles.
4. **Media weight.** Photographs would have cost hundreds of kilobytes and raised licensing
   questions. All nine images are self-authored SVG, which stay sharp at any size and total
   under 40 KB; the video is an 8-second 46 KB MP4 with `preload="none"` and a poster frame.
   The whole site is about 276 KB.

---

## 2. AI Assistance Declaration

**Tool used:** [e.g. Claude (Anthropic), accessed via claude.ai, July 2026]
**Used for:** initial page structure, first-draft copy, CSS scaffolding, JavaScript
patterns for the lightbox and form validation, and generation of the SVG artwork.
**Not used for:** [state what you did yourself — e.g. wireframes, palette selection,
final copy edits, testing, deployment, this report's reflection].

### 2.1 What I asked for, and what I changed

Fill this table with your own before/after pairs — the rubric rewards depth here, so show at
least three with real code, not descriptions.

| # | Initial AI output | My customised version | Why I changed it |
|---|---|---|---|
| 1 | Generic hero: "Welcome to our logistics company. We provide the best logistics solutions." | "Freight that arrives when we said it would." + a 34-word paragraph naming Port Botany, the four services and the tracking promise | The first version says nothing a competitor could not say. The rewrite makes a specific, checkable claim and meets the 20–50 word brief. |
| 2 | Lightbox that only opened and closed on click | Added focus management, Escape/arrow-key handling, `role="dialog"` + `aria-modal`, and a hidden live-region counter | The draft was unusable by keyboard, which would have cost accessibility marks and, more to the point, excluded users. |
| 3 | Form relying on HTML5 `required` alone | Layered JS: per-field `blur` validation, custom messages mapped from `ValidityState`, `aria-invalid`, inline `aria-live` error slots, a summary `role="status"` message | Default browser bubbles are inconsistent between browsers, vanish on blur, and are not announced reliably by screen readers. |
| 4 | [YOUR EXAMPLE] | [YOUR VERSION] | [YOUR REASON] |

### 2.2 How AI fitted into my workflow

*(Write 150–200 words in first person.)* Suggested shape: I used AI as a fast first
draft and a reviewer, not an author — I sketched wireframes first, asked for scaffolding,
then rejected/rewrote X, Y and Z because … The parts I understand best now are … The parts
I had to go and learn properly (from MDN / W3Schools) were …

---

## 3. Accessibility and Usability

### 3.1 Accessibility measures implemented

- **Semantic structure.** `header`, `nav`, `main`, `section`, `article`, `figure`,
  `figcaption`, `footer`, `dl`, `ol`, `table` with `caption`, `th scope`. `<div>` is used
  only where no semantic element fits.
- **Skip link.** A "Skip to main content" link is the first focusable element on every page,
  visible on focus.
- **Landmarks and labels.** `aria-label="Main"` on the nav; `aria-current="page"` on the
  active link; `aria-expanded` / `aria-controls` on the mobile menu button.
- **Images.** Every `<img>` carries a descriptive `alt`; the logo beside the wordmark uses
  `alt=""` because the text next to it already names the company.
- **Forms.** Every control has a visible `<label>`; hints and errors are wired with
  `aria-describedby`; errors set `aria-invalid="true"` and appear in `aria-live="polite"`
  regions; the submit result is announced through `role="status"`. Errors say what to do
  ("Use a 10-digit Australian number, for example 0412 345 678"), not just "invalid".
- **Keyboard.** All interactive elements are real `<button>` or `<a>` elements. Focus is
  visible everywhere via `:focus-visible` with a 3px amber outline. The lightbox traps and
  restores focus and responds to Escape and arrow keys.
- **Motion.** `@media (prefers-reduced-motion: reduce)` disables transitions and animations.
- **Colour contrast** (WCAG 2.1 AA requires 4.5:1 for body text):

  | Combination | Ratio | Result |
  |---|---|---|
  | `#0c2a2f` on `#eff2f0` (body) | ~14.9:1 | Pass AAA |
  | `#ffffff` on `#0c2a2f` (dark sections) | ~16.2:1 | Pass AAA |
  | `#145661` on `#eff2f0` (links) | ~6.9:1 | Pass AA |
  | `#0c2a2f` on `#ffc233` (primary button) | ~9.6:1 | Pass AAA |
  | `#a8321f` on `#ffffff` (errors) | ~6.4:1 | Pass AA |

  *Verify these yourself with the WebAIM contrast checker and paste in your screenshots —
  do not take the numbers on trust.*
- **Colour is never the only signal.** Invalid fields get a border change *and* a text
  message; shipment status shows a coloured pill *and* a word.

### 3.2 Responsiveness

Built mobile-first in behaviour, with three breakpoints:

| Width | What changes |
|---|---|
| ≤ 900px | Two-column hero and split sections stack; 3-up card grids become 2-up; stats go 4→2 |
| ≤ 720px | Nav collapses to a Menu button; gallery 3→2 columns; form rows stack; section padding reduces |
| ≤ 520px | Everything single-column; buttons go full width |

Fluid type (`clamp()`), `width: min(100% - 2.5rem, 1140px)` containers and
`aspect-ratio` on gallery thumbnails mean the layout adapts between breakpoints too, not
just at them.

**Testing:** [record what you actually did — Chrome DevTools device emulation at 320/375/768/1024/1440, plus a real phone. Note anything you fixed as a result.]

---

## 4. Screenshots and Visuals

Insert, with a one-line caption each:

1. Wireframes from the Week 4 submission (hand-drawn or Figma).
2. Home page — desktop, full page.
3. Home page — mobile (375px), showing the collapsed menu open.
4. Services page — the service-level table.
5. Track page — a successful lookup, showing the scan-history rail.
6. Track page — an error state (try `ANC-9999-XXX`).
7. Gallery — the lightbox open.
8. Contact — the form with validation errors, and the success message.
9. Wireframe vs final, side by side, for one page (required: "visual evidence of progression").
10. Validator results: W3C HTML validator, WAVE accessibility report, Lighthouse score.

---

## 5. Ethics and Privacy

- **No real user data is collected.** Both forms call `preventDefault()`; there is no
  backend, no database, no analytics, no cookies and no third-party tracking. The contact
  page states this on the page itself so a visitor is not misled into thinking their details
  were sent.
- **Honest prototyping.** The tracking page says plainly that it is a prototype with three
  sample consignments rather than implying a live system. The footer states the company is a
  student project, and social links point to platform home pages, not fake accounts.
- **Media provenance.** All nine images are original SVG artwork created for this project,
  and the video was generated from that same artwork. No stock photography, no third-party
  assets, no licensing ambiguity. Fonts are Archivo and IBM Plex, both open licensed
  (SIL OFL), served from Google Fonts.
- **Inclusive content.** Copy avoids jargon where plain words exist, names people in
  testimonials with varied backgrounds without stereotyping, and describes workers by role.
  Contact information uses `tel:` and `mailto:` so assistive technology and mobile devices
  handle them natively.
- **Accurate representation.** Every figure on the site (depot counts, transit times,
  on-time percentage) is clearly framed as belonging to a fictional business; nothing is
  attributed to a real company.
- **Academic integrity.** AI assistance is declared in section 2, all code was reviewed and
  modified by me, and no template or framework was used.

---

## 6. Reflection and Learning

*(Write 300–400 words in first person. Prompts, not a script:)*

- What was hardest? *(Mine would be accessible focus management in the lightbox — it looked
  finished long before it actually worked by keyboard.)*
- What changed your understanding? *(e.g. realising ARIA is a last resort — a real
  `<button>` beats `role="button"` every time.)*
- Where did AI genuinely help, and where did it slow you down or mislead you? Be specific
  and honest: markers can tell the difference between reflection and flattery.
- What feedback did you get at the Week 4 design review, and what did you change because of
  it?
- What would you do differently with another week? *(Candidates: real photography with
  `srcset`, a `prefers-color-scheme` dark variant, a proper backend for tracking.)*

---

## Appendix A — Technology summary

- HTML5, CSS3, vanilla JavaScript (ES5-compatible syntax, no libraries, no jQuery)
- One external stylesheet, ~700 lines, organised into 12 commented sections with a CSS
  custom-property token system
- One external script, ~230 lines, five self-contained init functions that each no-op if
  their markup is absent
- CSS3 features used: transitions, transforms (`translateY`, `scale`), rounded corners,
  box-shadows, linear and radial gradients, opacity, `clamp()`, `aspect-ratio`, CSS Grid
- Total page weight: ~276 KB for the entire site including all media
