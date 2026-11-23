# Berger Tech Consulting — Laravel site

A Laravel 13 rebuild of the layout, page structure and content of
[bergertechbd.com](https://www.bergertechbd.com/), using Blade, Tailwind CSS v4 and Vite.

## Requirements

- PHP 8.2+ (built against 8.4)
- Composer
- Node 20+ and npm

## Getting started

```bash
composer install && npm install && npm run build && php artisan migrate && php artisan serve
```

For active development, run the Vite dev server alongside `php artisan serve`:

```bash
npm run dev
```

## Pages

| Route | Name | Source page it mirrors |
| --- | --- | --- |
| `/` | `home` | Home |
| `/about` | `about` | Our Story |
| `/services` | `services.index` | Our Core Services |
| `/services/{slug}` | `services.show` | `sap`, `microsoft`, `cloud`, `dynamics-365-business-central` |
| `/solutions` | `solutions.index` | Our Solutions |
| `/solutions/{slug}` | `solutions.show` | `smart-delivery`, `logistic-automation`, `procure-to-pay`, `sharepoint-solution`, `salesforce-automation` |
| `/contact` | `contact` | Contact Us |
| `/appointment` | `appointment` | Set an Appointment |

An unknown service or solution slug returns a 404.

## Where the content lives

All page copy is config-driven, so text changes need no edits to Blade or PHP classes:

- `config/site.php` — company details, the mega-menu tree, home page sections
  (hero, service cards, customers, feature tiles, story, why-us, culture).
- `config/services_content.php` — the four service pages. Named to avoid clashing
  with Laravel's reserved `config/services.php`.
- `config/solutions.php` — the five solution pages, including the cards on the
  home page and the solutions index.

Adding a service or solution is a matter of adding a key to the relevant config
file; the routes, index pages and mega menu pick it up automatically.

## Forms

Both forms persist to the database and are covered by feature tests:

- `POST /contact` → `contact_messages`
- `POST /appointment` → `appointments`

Each is validated in a `FormRequest`, rate limited to 10 requests per minute per
IP, and protected by a hidden honeypot field (`website`) that must stay empty.
The honeypot is stripped before the model is created.

Submissions are stored, not emailed. To notify the team as well, add a
`Mail::to(...)->send(...)` call (or a queued notification) in
`ContactController@store` / `AppointmentController@store`.

## Design notes

The main colour is the header navy `#1f2750` (`brand-600` in `resources/css/app.css`;
it replaced the source site's blue `#0026DA`). Taken from the source site: Roboto for headings, Open Sans for body text, heavy
uppercase section titles, angled section dividers, and a full-screen mega menu
behind the hamburger.

## Images

The imagery is the original artwork from bergertechbd.com, downloaded once into
`public/images/` and served locally — nothing is hot-linked back to that site.

| Folder | What it holds |
| --- | --- |
| `logo/` | The Berger Tech wordmark (white on transparency, for the blue header and dark footer) |
| `hero/` | Page hero photographs |
| `partners/` | SAP, Microsoft Partner, Cloud and Dynamics 365 marks |
| `customers/` | The 21 customer logos in the marquee |
| `features/` | The four home page feature tiles |
| `why/` | The eight "Why We Are Premier Choice" icons |
| `solutions/` | Solution card artwork and solution page heroes |
| `services/` | Photos used beside the service page intros |
| `patterns/` | The services band and culture section backdrops |
| `story.jpg` | The Our Story photograph |

Every path lives in config, so swapping an image is a one-line change. Nine of
the source files were 1920x1000 photographs shipped as PNG (2–3 MB each); those
were re-encoded to JPEG, taking the folder from 31 MB to 9.2 MB.

Two notes on fidelity: the site's Microsoft hero is only a 600x200 banner, so
that page uses the full-size photograph from the same page and keeps the banner
as a partner mark; and a handful of customer logos carry no company name in the
file, so they are labelled generically in `config/site.php`.

`ImagesTest` fails the build if any configured or rendered image is missing from
`public/`, and names the exact config key at fault.

**Ownership:** these files are Berger Tech's assets (and their customers' marks),
reused here at your request. Replace them before putting this in front of anyone
who is not Berger Tech.

## Front-end structure

- `resources/views/layouts/app.blade.php` — shell, fonts, skip link.
- `resources/views/partials/` — `header` (sticky bar + mega menu), `footer`.
- `resources/views/components/` — `hero`, `cta-band`, `form-field`, `icon`.
- `resources/js/header.js` — sticky/transparent header, mega menu with focus trap
  and Escape to close.
- `resources/js/reveal.js` — IntersectionObserver scroll reveals.

Motion, including the customer marquee and scroll reveals, is disabled under
`prefers-reduced-motion`.

## Tests

```bash
php artisan test
```

23 tests cover every route, 404 handling, footer contact details, both forms
(valid, invalid, past-dated and honeypot cases), and the image wiring.

## Code style

```bash
./vendor/bin/pint
```
