# What changed in this update

All changes below were implemented and verified with an automated test suite
(`php artisan test` — 20 passing tests, 94 assertions).

## 1. Add Customer — only Name, Mobile, Address are mandatory
- `app/Livewire/Admin/Customers/Index.php`, `resources/views/livewire/admin/customers/index.blade.php`
- Name, Mobile and Address are now required; every other field is optional.
- Duplicate Mobile / Email are caught and shown as a friendly inline message
  ("A customer with this mobile number already exists.") instead of crashing.
- Live (on-blur) validation and required-field markers added.

## 2. Phone-verified customer feedback section (public site)
- New section on the home page (`#feedback`, added to the nav menus).
- New: `app/Models/Feedback.php`, `app/Livewire/Public/FeedbackSection.php`,
  `resources/views/livewire/public/feedback-section.blade.php`,
  migration `..._create_feedback_table.php`.
- Reviews are NOT random: the visitor enters name / mobile / rating / message,
  receives a 6-digit OTP on their mobile, and the review is only stored and
  displayed after the code is verified. Only verified reviews are shown.
- OTP delivery is logged and (in debug mode) surfaced in-app so it works
  without an SMS gateway. To go live, wire `FeedbackSection::sendCode()` to
  your SMS / WhatsApp provider.
- 5 demo reviews are seeded by `DemoDataSeeder`.

## 3. Logo, favicon, meta tags & SEO
- New brand logo / favicon set under `public/icons/` plus `public/favicon.ico`
  and `public/favicon.svg`; PWA icons (192 / 512 / maskable) and an Apple touch
  icon; an Open Graph share image (`public/icons/og-image.png`).
- `resources/views/layouts/public.blade.php`: meta description, keywords,
  author, canonical, full Open Graph + Twitter Card tags, and favicon links.
- Favicons also added to admin, rider and login layouts.
- `public/robots.txt` updated for SEO.

## 4. Validation & friendly errors on every form
- Duplicate / DB errors now surface on the form instead of a 500 page across:
  Customers, Categories (duplicate names blocked), Branches, Riders, Services,
  Items, Price lists and the POS quick-add customer.

## 5. Bug fixes
- `app/Enums/OrderStatus.php`: `pipeline()` was missing the **Dry Cleaning**
  stage (returned 8 of 9 stages), which broke the order-tracking timeline.
- Test suite could not run: added the missing `mockery/mockery` dev dependency
  (with `hamcrest/hamcrest-php`) to `composer.json` / `composer.lock`, and a
  test `APP_KEY` to `phpunit.xml`.

## Tests
- `tests/Feature/SmokeTest.php` (new) exercises every public + admin page,
  the customer / feedback / contact forms, duplicate handling, SEO/icons and a
  full POS checkout.
