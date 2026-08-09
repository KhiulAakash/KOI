# Anchorline Logistics — ICT726 Assignment 3

A static website for a fictional Australian freight and logistics operator. Built from
scratch with HTML5, CSS3 and vanilla JavaScript. No frameworks, no templates, no
server-side code.

## Files

```
index.html          Home
services.html       Services detail + service level table
track.html          Shipment tracking prototype (JS lookup + validation)
gallery.html        Media page: video + thumbnail gallery with lightbox
about.html          Company story and timeline
contact.html        Contact details + validated enquiry form
css/style.css       Single external stylesheet (assignment allows a max of 2)
js/main.js          Nav, lightbox, tracker, form validation
img/*.svg           Nine self-created SVG illustrations
media/              Self-generated MP4 loop + JPG poster frame
```

## Running it locally

Open `index.html` in a browser. Everything is client-side, so no build step or server is
needed. (If you prefer a local server: `python3 -m http.server` inside this folder.)

## Publishing to GitHub Pages

1. Create a public repo, e.g. `anchorline-logistics`.
2. Upload every file in this folder, keeping the folder structure. `index.html` must sit at
   the repository root.
3. Repo → **Settings** → **Pages** → Source: *Deploy from a branch* → Branch: `main`,
   folder `/ (root)` → **Save**.
4. Wait about a minute, then open `https://<your-username>.github.io/anchorline-logistics/`.
5. Paste that link into Moodle and into section 1 of your report.

Netlify alternative: drag this folder onto <https://app.netlify.com/drop>.

## Assignment requirements → where they are met

| Requirement | Where |
|---|---|
| `index.html` homepage, business name, 20–50 word paragraph | `index.html` hero (34 words) |
| Three or more additional pages | services, track, gallery, about (4) |
| Contact page with styled `<form>`, HTML5 + JS validation, error and success feedback | `contact.html` + `initContactForm()` |
| Media page with interactive thumbnails opening larger versions | `gallery.html` + `initLightbox()` |
| At least three pieces of media | 9 SVG images, 1 MP4 video, 1 JPG poster |
| Header with logo/banner | `.site-header` on every page |
| Consistent navigation | shared `<nav>` with `aria-current="page"` |
| Footer | `.site-footer` on every page |
| HTML5 semantic tags, minimal `<div>` | `header/nav/main/section/article/figure/footer` |
| Max 2 external stylesheets | 1 (`css/style.css`) |
| Three or more CSS3 features | transitions, transforms, rounded corners, shadows, gradients, opacity (6) |
| Media queries / responsive | four breakpoints: 900px, 720px, 520px + `prefers-reduced-motion` |
| Alt attributes | every `<img>`; decorative logo uses `alt=""` |
| ARIA | `aria-current`, `aria-expanded`, `aria-controls`, `aria-live`, `aria-invalid`, `aria-describedby`, `role="dialog"`, `aria-modal`, `role="alert"`, skip link |
| Contrast | palette checked against WCAG AA (see report) |
| Hosting | GitHub Pages, steps above |

## Before you submit

- [ ] Replace "Anchorline Logistics" if your tutor assigned a specific business name.
- [ ] Put your name and student ID in the footer and in the CSS header comment.
- [ ] Take screenshots of every page (desktop + mobile) for the report.
- [ ] Run the site through <https://validator.w3.org/> and <https://wave.webaim.org/>.
- [ ] Publish, test the live link in a private window, paste it into Moodle.
- [ ] Complete and submit `REPORT-DRAFT.md` as a Word document.
