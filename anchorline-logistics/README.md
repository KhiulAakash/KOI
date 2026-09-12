 # Anchorline Logistics

A dynamic web application for a fictional Australian freight and logistics operator, built
with PHP, MySQL, HTML5, CSS3 and vanilla JavaScript. Started as a static HTML site (ICT726
Assignment 3) and rebuilt here with server-side authentication, role-based access control,
and two database-backed CRUD workflows.

## Stack

- **PHP** (PDO + prepared statements, session-based auth) — tested against XAMPP's bundled PHP
- **MySQL** — schema and seed data in `sql/schema.sql`
- HTML5, CSS3, vanilla JavaScript (progressive enhancement — every form works without JS)

## Key functionality

- **Auth & roles.** Register / log in / log out. Three roles — `admin`, `member`, `normal` —
  enforced on the server (`includes/auth.php`) on every protected page, not just hidden in
  the UI. Passwords are hashed with `password_hash()` / `password_verify()`.
- **Shipments (CRUD).** `admin/consignments.php` — admins create, edit and delete consignment
  records. `member/scan.php` — staff (member or admin) add scan events, which advance a
  consignment's status. `track.php` is the public, database-backed lookup (replaces the old
  hardcoded JS demo data).
- **Enquiries (CRUD).** `contact.php` inserts real rows into `enquiries`; `admin/enquiries.php`
  lets admins review them and update status; logged-in customers see their own on
  `dashboard.php`.
- **Security.** PDO prepared statements everywhere, CSRF tokens on every state-changing form,
  server-side validation mirrored (not replaced) by client-side JS, `HttpOnly` session
  cookies, generic login-failure messages.
- **Accessibility / SEO / privacy** carried over and extended from the static build — see
  `includes/header.php` for canonical/OG tags and JSON-LD, `privacy.php` for the privacy
  notice, `robots.txt` / `sitemap.xml`.

## Files

```
config.php              App bootstrap: constants, session, requires includes/*
includes/db.php          PDO connection
includes/auth.php        Session auth + role gate (require_login, require_role) + CSRF
includes/functions.php   e(), flash messages, validators, enum label maps
includes/header.php      Shared <head> + session-aware nav
includes/footer.php      Shared footer + closing tags

index.php, services.php, gallery.php, about.php   Static-content pages
track.php                 Public shipment tracker (DB lookup)
contact.php                Public enquiry form -> enquiries table
privacy.php                 Privacy notice
register.php / login.php / logout.php   Auth
dashboard.php               Role-aware landing page after login
admin/consignments.php      Admin: consignment CRUD
admin/enquiries.php         Admin: enquiry status management
admin/users.php             Admin: user role management
member/scan.php              Member/admin: add a scan event

sql/schema.sql   Table definitions + seed data (2 users, 3 sample consignments/scans)
css/style.css, js/main.js   Shared styling/behaviour (extended, not replaced, from the static build)
img/*.svg, media/*   Original SVG illustrations + generated video loop
robots.txt, sitemap.xml   SEO
```

## Running it locally (XAMPP)

1. Install/start [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP).
2. Serve this folder from `htdocs`. Either copy it in, or link it so the git repo stays the
   single source of truth:
   ```
   mklink /J "C:\xampp\htdocs\anchorline-logistics" "<path to this repo>"
   ```
3. Create the database and import the schema:
   ```
   C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE anchorline CHARACTER SET utf8mb4;"
   C:\xampp\mysql\bin\mysql.exe -u root anchorline < sql\schema.sql
   ```
4. Start Apache and MySQL from the XAMPP control panel (or `apache_start.bat` /
   `mysql_start.bat`).
5. Open `http://localhost/anchorline-logistics/index.php`.

If your XAMPP `htdocs` folder name or DB credentials differ, edit `BASE_URL` / `DB_*` in
`config.php`.

### Demo accounts (from the seed data)

| Role | Email | Password |
|---|---|---|
| admin | `admin@anchorline.example` | `Admin@12345` |
| member | `member@anchorline.example` | `Member@12345` |

Register a new account to see the `normal` role. Change or remove these before this ever
leaves a local dev machine.

### Sample waybills

`ANC-4471-QLD`, `ANC-7726-VIC`, `ANC-1039-WA` — seeded with scan history, usable immediately
on `track.php`.

## Assignment requirements -> where they are met

| Requirement | Where |
|---|---|
| User registration, login, logout | `register.php`, `login.php`, `logout.php` |
| Role-based access (admin / member / normal) | `includes/auth.php` `require_role()`, enforced on every `admin/`/`member/` page |
| Secure password storage | `password_hash()` / `password_verify()` in `register.php`, `login.php` |
| Database schema with relationships | `sql/schema.sql` — `users` 1→N `consignments`, `consignments` 1→N `scan_events`, `users` 1→N `enquiries` |
| PHP + MySQL CRUD | `admin/consignments.php` (create/read/update/delete), `member/scan.php` (create), `admin/enquiries.php` (read/update) |
| Two validated data-input forms | `contact.php` (enquiries), `register.php`/`admin/consignments.php` (accounts/shipments) — all server- and client-validated |
| Semantic HTML5, ARIA, responsive, media | Carried over from the static build; see `css/style.css` sections 4/11 |
| SEO: titles, meta, canonical, OG, structured data, sitemap | `includes/header.php`, `robots.txt`, `sitemap.xml` |
| Privacy notice, secure data handling | `privacy.php`; prepared statements + CSRF throughout |

## Before you submit

- [ ] Change the seeded demo passwords (or note in your report that this is a teaching
      environment).
- [ ] Take fresh screenshots of every role's view for the report.
- [ ] Run the site through the W3C HTML validator and WAVE (validators can't reach a
      localhost site directly — either deploy to a public PHP/MySQL host first, or save a
      rendered page and upload it).
- [ ] Update `REPORT-DRAFT.md` for this assignment's report requirements.
      
    ## Recent Improvements

The following improvements have recently been made to the Anchorline Logistics system:

- Improved user account management functionality.
- Added phone number and address fields to user profiles.
- Added forgot-password and password reset functionality.
- Improved database integration for storing user information.
- Added a quick-answer chatbot to improve user support.
- Improved the overall usability and navigation of the website.
