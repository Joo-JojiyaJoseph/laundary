# Laundrix — Premium Laundry SaaS

A multi-branch laundry & dry-cleaning SaaS built with **Laravel 13 + Livewire 4 + Alpine.js + Tailwind CSS 4**, featuring realtime order tracking via **Pusher Channels + Laravel Echo**, GSAP/Motion One animations, glassmorphism UI, AI features (OpenAI), WhatsApp Cloud API automation, Firebase push notifications, QR-code invoice tracking, a POS terminal, loyalty tiers and a PWA customer experience.

> **What you received:** a complete, production-structured scaffold. Vendor dependencies are not bundled — run `composer install` and `npm install` locally (see Quick start). Some modules from the full specification (rider GPS app screens, full reports suite, team chat UI) are architected at the model/event/service layer and ready to be extended.

> **Laravel 13** (released 17 Mar 2026) is the target framework — `composer.json` requires `laravel/framework: ^13.0` and PHP **8.3+** (the Docker image already ships php:8.3-fpm). Laravel 13 introduced no breaking changes over 12, so everything here runs as-is. Optionally you can adopt its new PHP attribute syntax (e.g. `#[Table]`, `#[Fillable]` on models) and the first-party Laravel AI SDK as you extend the app.

---

## Tech stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.3+, MySQL 8, Redis |
| Frontend | Livewire 4, Alpine.js (bundled with Livewire), Tailwind CSS 4, Vite |
| Animations | GSAP (ScrollTrigger), Motion One, Livewire morphing |
| Realtime | Pusher Channels + Laravel Echo (no Reverb) |
| AI | OpenAI (HTTP client, no SDK needed) — forecasts, pricing, assistant, WhatsApp writer |
| Notifications | WhatsApp Cloud API, Firebase Cloud Messaging, Mail |
| Payments | Razorpay, Stripe, Cash / UPI / Card / Bank transfer |
| Auth & security | Sanctum, Spatie Permission, Spatie Activity Log, signed tracking URLs |
| Files & exports | Spatie Media Library, PhpSpreadsheet, DomPDF, Simple QR Code |
| DevOps | Docker (nginx, php-fpm, mysql, redis, queue, scheduler), Supervisor config for bare metal |

## Quick start (local)

```bash
# 1. Install PHP dependencies
composer install

# 2. Environment
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate

# 3. Database — create an empty `laundrix` database, set DB_* in .env, then:
php artisan migrate --seed
php artisan db:seed --class=DemoDataSeeder   # optional: 35 days of realistic demo orders
# riders log in at /login with e.g. arun.rider@laundrix.ai / password -> redirected to /rider

# 4. Frontend
npm install
npm run dev        # or: npm run build

# 5. Serve
php artisan serve  # or use Laravel Herd: `herd link laundrix` inside the folder
```

The project ships with the full Laravel 13 skeleton (artisan, bootstrap/,
config/, storage/) plus in-repo migrations for Spatie Permission, Activity
Log, Media Library, Sanctum tokens and the cache/queue/session tables — no
`vendor:publish` step is needed before migrating.

**Seeded login** — `admin@laundrix.ai` / `password` (super-admin).
The seeder also creates the HQ branch (Kochi), 16 granular permissions, 6 roles
(super-admin, admin, branch-manager, counter-staff, rider, customer) and 9
services with priced products (Dry Cleaning, Wash & Fold, Premium Laundry,
Steam Ironing, Shoe Cleaning, Curtain Cleaning, Stain Removal, Toy Cleaning,
Bag Cleaning).

## Quick start (Docker)

```bash
cp .env.example .env          # set DB_HOST=mysql, REDIS_HOST=redis
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
npm install && npm run build  # or add a node service if you prefer
```

The stack runs nginx (port 80), php-fpm, MySQL 8.4, Redis 7, a queue worker
and the scheduler. For a bare Ubuntu server, use
`docker/supervisor/worker.conf` with Supervisor instead of the queue/scheduler
containers, and terminate SSL at Cloudflare or certbot.

## Required service credentials (.env)

| Feature | Keys |
|---|---|
| Realtime (Pusher) | `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_CLUSTER` + matching `VITE_PUSHER_*` |
| AI | `OPENAI_API_KEY`, `OPENAI_MODEL` (default `gpt-4o-mini`) |
| WhatsApp Cloud API | `WHATSAPP_TOKEN`, `WHATSAPP_PHONE_NUMBER_ID`, `WHATSAPP_WEBHOOK_VERIFY_TOKEN` |
| Push | `FIREBASE_CREDENTIALS` (service-account JSON path), `FIREBASE_PROJECT_ID` |
| Payments | `RAZORPAY_KEY`/`RAZORPAY_SECRET`, `STRIPE_KEY`/`STRIPE_SECRET` |
| Cache/queues | `REDIS_HOST`, `QUEUE_CONNECTION=redis`, `CACHE_STORE=redis` |

All realtime features degrade gracefully when Pusher keys are absent; the AI
forecast widget shows a skeleton until `OPENAI_API_KEY` is configured.

### Dependency notes (Laravel 13)

- **OpenAI** is called through Laravel's built-in HTTP client
  (`App\Services\AI\OpenAiClient`) — the `openai-php/laravel` package was
  intentionally **not** used because it does not yet support Laravel 13.
  No extra SDK is required.
- **Livewire 4** bundles its own Alpine.js. The app uses the manual ESM
  bundle (`livewire.esm` imported in `resources/js/app.js` +
  `@livewireScriptConfig` in layouts), so do not add a separate `alpinejs`
  npm package or call `Alpine.start()` yourself.
- All other constraints were verified against Laravel 13:
  phpoffice/phpspreadsheet ^5.3 (Laravel Excel has no stable PHP 8.5 release yet), kreait/laravel-firebase ^7.2,
  barryvdh/laravel-dompdf ^3.1, Spatie packages on their current majors.

## What's included

### Public website (GSAP-animated, glassmorphism, dark/light)
- **Home** — hero with live order-journey signature card, animated counters,
  services grid, 4-step process timeline, testimonials, pricing tiers, FAQ
  accordion, CTA.
- **About** — mission/vision, animated stats, team, 2019–2025 milestones.
- **Services** — all nine services with pricing, turnaround, benefits and FAQs.
- **Contact** — validated Livewire form, Google Map, branch list, WhatsApp button.
- Glass sticky nav with `wire:navigate` page transitions, cookie consent,
  floating WhatsApp button, reduced-motion support.

### QR invoice tracking — `/track/{INV-no}?t={token}`
Every invoice stores a 48-hex-char secret token; `Invoice::trackingUrl()`
builds the QR target. The tracker page validates the token with
`hash_equals`, shows an animated status timeline + progress bar, items and
expected delivery, and **updates live** via the `orders.{id}` Pusher channel —
no login required.

### Admin
- **Dashboard** — six animated counters (today's orders, pending, delivered,
  revenue, customers, riders online), 30-day Chart.js revenue chart, AI
  revenue-forecast widget, 60-second polling + branch-channel realtime events.
- **Orders** — searchable, filterable list (status, payment, branch-scoped)
  plus a full detail page: clickable 9-stage status pipeline, status history,
  rider assignment, delivery OTP, item tags, payment recording with automatic
  paid/partial/unpaid rollup, and the invoice QR + tracking link rendered
  inline for printing.
- **Payments & collections** — every transaction with method filters, plus an
  Outstanding tab listing all dues with one-click jump to collect.
- **Catalogue management** — full CRUD for Services, Product Categories,
  Items (name/category/service/UOM/price/priority/status) and Price Lists
  (branch / customer / VIP / seasonal / promotional rules with date windows).
- **Customers** — searchable book with tier filters, VIP flag, birthday,
  branch assignment, WhatsApp deep links and order counts.
- **Branches & riders** — branch cards with open/closed toggles; rider
  onboarding creates the user + role + rider profile in one transaction, with
  online status and last-seen location.
- **POS terminal** — customer search, service filter chips, product grid with
  tier/branch-aware pricing (`Product::priceFor()`), cart, discount/tax/advance,
  six payment methods; checkout atomically creates the order, items, advance
  payment, invoice and fires the first status transition + Pusher broadcast.

### Domain layer
- Order pipeline enum: pickup_scheduled → picked_up → washing → dry_cleaning →
  ironing → quality_check → ready → out_for_delivery → delivered, with
  `Order::transitionTo()` logging + broadcasting `OrderStatusUpdated` on
  public, customer and branch channels.
- Loyalty: points ledger + auto tiers (Gold ≥ 1 500 pts, Platinum ≥ 5 000 pts),
  referral codes, coupons table.
- Price resolution: customer → VIP → seasonal/promotional → branch → base.
- `WhatsAppService` (templates + invoice PDFs), `AiAssistantService`
  (customer "where is my order"), `AiInsightsService` (forecast, pricing
  suggestions, WhatsApp message writer).
- Events for rider GPS (`RiderLocationUpdated`) and team/support chat
  (`ChatMessageSent`) with auth'd private channels in `routes/channels.php`.

### PWA
`public/manifest.json` (installable, shortcuts, theme color) +
`public/sw.js` (network-first pages, cache-first assets, offline fallback,
push notification + click handling). Add your icons at
`public/icons/icon-192.png`, `icon-512.png`, `maskable-512.png`.

## Extending the scaffold

The models, migrations, events, channels and services for riders, support
tickets, team chat, loyalty and payments are in place — adding the remaining
CRUD/Livewire screens follows the same pattern as `Admin\Dashboard` and
`Pos\Terminal`:

1. `php artisan make:livewire Admin/Customers/Index`
2. Render with `->layout('layouts.admin', ['title' => '...'])`
3. Reuse the `.glass`, `.card-float`, `data-reveal`, `data-counter` utilities.

## Security notes

- Tracking URLs are unguessable (48-hex token, timing-safe comparison).
- All admin routes sit behind `auth` + role middleware; broadcasting channels
  are authorized in `routes/channels.php`.
- Spatie Activity Log records changes on key models; sessions, OTP codes,
  login history and device tokens have dedicated tables.
- Configure `SESSION_SECURE_COOKIE=true` and run behind Cloudflare SSL in
  production.

---

Built with care — wash, track, delight. 🧺


## Rider login shows 403 "User does not have the right roles"?
This is a stale Spatie permission cache from the very first migration. Fix it once:

```bash
php artisan optimize:clear
php artisan permission:cache-reset
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoDataSeeder
```

The rider gate now re-checks roles directly against the database, so this should not recur.


## What changed in this build
- **Catalogue hierarchy**: Category → Service → Item & Price. Create categories first, then add services under a category, then items+price under each service.
- **Customer creation** now includes an address field.
- **POS**: items already in the cart are highlighted with a quantity badge; each cart line has a remove (×) button.
- **Order numbers** use the `LDS00001` format; public tracking uses the **order number** (not invoice number).
- **Order detail cards** are now crisp (reduced glass blur) for better readability.
- **Removed packages**: Pusher, Razorpay, Stripe, Predis (PHP) and pusher-js, motion (JS). Broadcasting defaults to `log`.
- **Frontend toolchain**: Vite 7 with an `esbuild ^0.28.1` override — `npm install` reports **0 vulnerabilities**.

> After pulling: `composer install`, then `php artisan migrate`, then `npm install && npm run build` (or `npm run dev`).
