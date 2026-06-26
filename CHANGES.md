# Laundrix — Feature update

This package contains **only the new and changed files**. Unzip it at the root
of your existing Laundrix project, letting it overwrite/merge by path.

## How to apply

1. **Back up your project** (or commit your current state to git first).
2. Unzip `laundrix-changes.zip` into your project root so the folders merge:
   ```bash
   unzip laundrix-changes.zip -d /path/to/your/laundrix
   ```
3. **Run the migration** (creates the `feedback` table):
   ```bash
   php artisan migrate
   ```
4. *(Optional)* seed a few sample reviews so the public Reviews section and the
   admin Feedback screen have content immediately:
   ```bash
   php artisan db:seed --class=FeedbackSeeder
   ```
5. Clear caches and rebuild assets if you cache views/config in your environment:
   ```bash
   php artisan optimize:clear
   npm run build      # or: npm run dev
   ```

> Note: this project's composer dependencies require **PHP 8.4.1+**. Make sure
> your runtime matches before running artisan.

---

## What changed, by request

### 1. User feedback — no OTP, admin approval, only approved shown on the site
- New `feedback` table (migration `2026_06_25_000001_create_feedback_table.php`)
  and `App\Models\Feedback`.
- Public submit form (**name + star rating + message**) — no OTP. New entries are
  saved with `is_approved = false`, so they are **not** shown publicly until an
  admin approves them.
- New homepage **“Reviews”** section (`#feedback`, with a nav-bar link) that shows
  **only approved** feedback, plus an average-rating header.
- Admin **Feedback** page (sidebar → Operations → Feedback) with
  **Pending / Approved / All** tabs and **Approve / Hide / Delete** actions, a
  search box and the date filter. Only approved items reach the website.

Files: `app/Models/Feedback.php`, `app/Livewire/Public/FeedbackSection.php`,
`resources/views/livewire/public/feedback-section.blade.php`,
`app/Livewire/Admin/Feedback/Index.php`,
`resources/views/livewire/admin/feedback/index.blade.php`,
`database/seeders/FeedbackSeeder.php`.

### 2. Admin date filters (Today / Week / Month / Year / Custom)
- Reusable trait `App\Livewire\Concerns\WithDateFilter` and a reusable UI
  component `<x-admin.date-filter />`.
- Wired into **Dashboard, Orders, Payments, Customers, Feedback** and both
  **Reports**. Custom mode reveals from/to date pickers.

Files: `app/Livewire/Concerns/WithDateFilter.php`,
`resources/views/components/admin/date-filter.blade.php` (+ the components/views
listed below).

### 3. Sidebar click now reloads the page (loads latest data)
- Removed `wire:navigate` from the sidebar links (and the logo) in
  `resources/views/layouts/admin.blade.php`, so every sidebar click performs a
  **full page load** and always pulls fresh data. On mobile the sidebar also
  auto-closes after you tap a link.

### 4. Payments — outstanding shown first, then transactions
- `app/Livewire/Admin/Payments/Index.php` now defaults to the **Outstanding**
  tab, and the view shows **Outstanding** first, **Transactions** second. The
  date filter was added to the transactions list.

### 5. Customer detail (show) page with their orders & payments
- New route `admin.customers.show` and component
  `app/Livewire/Admin/Customers/Show.php` + view.
- Reached via a new **View** button on each row of the Customers list.
- Shows the profile, totals (billed / paid / outstanding) and tabbed
  **Orders** and **Payments** for that customer.

### 6. Reports section (Order report + Payment report) with filters + export
- New sidebar group **Reports** → **Order report** and **Payment report**.
- **Order report** filters: date/period, customer, rider, status, payment status.
- **Payment report** filters: date/period, customer, method, type.
- Both show summary tiles and an **Export Excel** button that downloads a real
  `.xlsx` file (via PhpSpreadsheet). The **Date** column is written as a proper
  Excel date (`dd-mm-yyyy hh:mm`) and every column is auto-sized, so the date is
  always fully visible instead of showing as `####`. Amounts export as numbers
  and order numbers/mobiles stay as text (no lost leading zeros).

Files: `app/Livewire/Admin/Reports/Orders.php`,
`app/Livewire/Admin/Reports/Payments.php`,
`resources/views/livewire/admin/reports/orders.blade.php`,
`resources/views/livewire/admin/reports/payments.blade.php`.

### 7. Mobile responsiveness
- Wide tables are wrapped in horizontal-scroll containers with sensible
  min-widths so they no longer break the layout on phones.
- Filter bars stack vertically on small screens and spread out on larger ones.
- The admin shell was already responsive; these changes make every new and
  edited page comfortable on mobile.

### 8. All admin tables become cards on mobile
- On screens narrower than 768px, every admin data table (`table-admin`) now
  renders as a stack of **cards** — one card per row, with each value labelled
  by its column name — instead of a sideways-scrolling table. On tablet/desktop
  the normal table is unchanged.
- Implemented once, centrally: a `.table-cards` CSS block (in
  `resources/css/app.css`) plus a small script in the admin layout that copies
  each column header into the matching cell's `data-label`. The script re-runs
  after every Livewire update (pagination, filters, tab switches), so cards
  stay correctly labelled.
- Applied to Orders, Order detail (items), Payments (transactions + outstanding),
  Customers, Customer detail (orders + payments), Categories, Services, Items,
  Price lists, and both Reports. Riders and Branches were already card grids.

> **Important:** because Tailwind compiles styles at build time, you must run
> `npm run build` (or `npm run dev`) after applying this update for the card
> styling to take effect.

---

## Files in this package

**New**
- `database/migrations/2026_06_25_000001_create_feedback_table.php`
- `database/seeders/FeedbackSeeder.php`
- `app/Models/Feedback.php`
- `app/Livewire/Concerns/WithDateFilter.php`
- `app/Livewire/Concerns/ExportsSpreadsheet.php`
- `app/Livewire/Public/FeedbackSection.php`
- `app/Livewire/Admin/Feedback/Index.php`
- `app/Livewire/Admin/Customers/Show.php`
- `app/Livewire/Admin/Reports/Orders.php`
- `app/Livewire/Admin/Reports/Payments.php`
- `resources/views/components/admin/date-filter.blade.php`
- `resources/views/livewire/public/feedback-section.blade.php`
- `resources/views/livewire/admin/feedback/index.blade.php`
- `resources/views/livewire/admin/customers/show.blade.php`
- `resources/views/livewire/admin/reports/orders.blade.php`
- `resources/views/livewire/admin/reports/payments.blade.php`

**Modified**
- `routes/web.php`
- `resources/css/app.css`
- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/public.blade.php`
- `resources/views/components/icon.blade.php`
- `resources/views/livewire/public/home-onepage.blade.php`
- `resources/views/livewire/admin/dashboard.blade.php`
- `resources/views/livewire/admin/orders/index.blade.php`
- `resources/views/livewire/admin/orders/show.blade.php`
- `resources/views/livewire/admin/payments/index.blade.php`
- `resources/views/livewire/admin/customers/index.blade.php`
- `resources/views/livewire/admin/categories/index.blade.php`
- `resources/views/livewire/admin/products/index.blade.php`
- `resources/views/livewire/admin/services/index.blade.php`
- `resources/views/livewire/admin/price-lists/index.blade.php`
- `app/Livewire/Admin/Dashboard.php`
- `app/Livewire/Admin/Orders/Index.php`
- `app/Livewire/Admin/Payments/Index.php`
- `app/Livewire/Admin/Customers/Index.php`

## Verification done
Every PHP file passes `php -l`, and every Blade view was compiled to PHP and
syntax-checked. The app was not booted at runtime here because the bundled
dependencies require PHP 8.4 (only 8.3 was available in this environment), so
please smoke-test the pages after running the migration.
