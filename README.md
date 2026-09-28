# FinTrack

A self-hosted personal finance tracker for people who want their money data on their own machine. Track accounts, log transactions, set category budgets, and share an account with a partner or housemate — without handing a third party read access to your bank.

![CI](https://github.com/zakaria17amir/Fintrack/actions/workflows/ci.yml/badge.svg)
![Docker](https://img.shields.io/badge/image-ghcr.io-2496ED)
![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20)
![Tests](https://img.shields.io/badge/tests-62%20passing-brightgreen)
![React](https://img.shields.io/badge/React-19-61DAFB)
![TypeScript](https://img.shields.io/badge/TypeScript-strict-3178C6)

![Reports](docs/screenshots/reports.png)

## Why this exists

Most budgeting apps want bank credentials and a subscription. FinTrack is the opposite: a small Laravel app you run yourself, backed by a single SQLite file you can copy, back up, or delete. It started as a semester project and I kept building on it because I actually wanted to use it.

The interesting constraint is **shared accounts**. A joint account isn't just "two users see the same rows" — one person may own it, another may only be allowed to look. That's modelled as a many-to-many with a permission level on the pivot, and it's what most of the authorization logic exists to enforce.

## Features

- **Accounts** — checking, savings, credit, cash. Balances update automatically as transactions are created, edited, or deleted.
- **Transactions** — income/expense with category, status (pending/cleared/cancelled), notes, recurring intervals, and receipt image upload.
- **Budgets** — monthly per-category limits with live progress bars and over-budget warnings.
- **Reports** — a React + TypeScript page inside the Blade app: date-range and multi-category filters that update the figures without a page reload, a spending-by-category pie chart, a monthly income-vs-expense trend (Recharts), and a breakdown table. Served by a validated JSON endpoint that computes every figure in three grouped SQL queries.
- **Account sharing** — invite another user to an account as viewer or editor; permissions are enforced server-side on every route.
- **Admin panel** — user management, category CRUD, and read-only oversight of all transactions, gated by role middleware.

| Transactions | Admin panel |
|---|---|
| ![Transactions](docs/screenshots/transactions.png) | ![Admin](docs/screenshots/admin.png) |

## Tech stack

| Layer | Choice |
|---|---|
| Framework | Laravel 13 (PHP 8.4+) |
| Auth | Laravel Breeze (Blade) |
| Database | SQLite |
| Frontend | Blade + Tailwind CSS 3, Flowbite, Alpine.js; Reports page in React 19 + TypeScript (strict) with Recharts |
| Charts | Recharts (reports), Chart.js (dashboard) |
| Build | Vite 8 |
| Delivery | Docker (FrankenPHP), published to GHCR by GitHub Actions |
| Tests | PHPUnit feature tests, Vitest + React Testing Library, Pint in CI |

## Run with Docker

Every push to `main` that passes CI publishes an image to GitHub Container Registry. It runs the app
on [FrankenPHP](https://frankenphp.dev) on port 8080, with the SQLite database and uploaded receipts
on volumes:

```bash
docker run -p 8080:8080 -e SEED_DEMO=true   -v fintrack-data:/data -v fintrack-storage:/app/storage/app   ghcr.io/zakaria17amir/fintrack:latest
```

Or build from source with `docker compose up --build`, then open <http://localhost:8080> and sign in
with a demo account below.

On start the container creates the database if needed, generates an `APP_KEY` once (kept in
`/data/app_key` unless you pass one), runs migrations, seeds demo data on first start when
`SEED_DEMO=true`, links public storage and caches config and routes.

The CI `publish` job only runs after the test job passes. It builds the image, starts it and runs
`docker/smoke-test.sh` — health check, guest redirect, a real form login with CSRF, the React reports
page and its JSON endpoint, and a `422` for a bad filter — before pushing `latest` and `sha-<commit>`
tags.

## Quick start

```bash
git clone https://github.com/zakaria17amir/Fintrack.git
cd Fintrack
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Open http://127.0.0.1:8000.

### Demo accounts

Created by the seeder. Change or remove them before deploying anywhere real.

| Role | Email | Password |
|---|---|---|
| Admin | `admin@fintrack.com` | `password` |
| User | `john@example.com` | `password` |

To develop with hot reload, run `composer run dev` — it starts the PHP server, queue worker, log tailer, and Vite together.

## Data model

Five domain tables plus Laravel's defaults.

```
User ──1:N──> Account ──1:N──> Transaction <──N:1── Category
 │                                  ^                   │
 │                                  │                   │
 └──────────1:N────────────────────-┘                   │
 │                                                      │
 └──1:N──> Budget ──────────────N:1─────────────────────┘
 │
 └──N:N──> Account   (account_user pivot, permission: viewer | editor)
```

- **`transactions`** is the wide table (13 columns) and exercises most of the type range: integer, boolean, date, timestamp, and three enums (`type`, `status`, `recurring_interval`).
- **`account_user`** is the sharing pivot, carrying a `permission` enum rather than being a bare join table.

**Money is stored as integer minor units** (cents), never floats, and divided by 100 only at display time. Floating-point currency arithmetic silently loses precision — `0.1 + 0.2 != 0.3` — and that is not acceptable in a ledger.

## Project structure

```
app/
  Http/Controllers/        User-facing controllers
  Http/Controllers/Admin/  Admin-only controllers
  Http/Requests/           Form request validation
  Http/Middleware/         Role gate for /admin
  Models/                  Account, Budget, Category, Transaction, User
database/
  migrations/  seeders/  factories/
docker/                    Container entrypoint and smoke test
app/Services/ReportData.php  Report aggregation (totals, by category, by month)
resources/js/reports/      React + TypeScript reports island and its tests
resources/views/           Blade templates
routes/web.php             Application routes
tests/                     PHPUnit feature + unit tests
```

## Testing

```bash
composer run test     # PHPUnit
npm test              # Vitest + React Testing Library
npm run typecheck     # tsc, strict
```

54 PHPUnit feature tests (150 assertions) and 8 Vitest tests run on every push, alongside a Pint style check and a strict TypeScript check. Beyond the Breeze authentication and profile suite, they cover the rules that matter for a ledger:

| Suite | What it pins down |
| --- | --- |
| `TransactionBalanceTest` | Every write moves the balance exactly once — create, change amount, flip expense/income, move between accounts, delete — and a failed balance update leaves no orphaned transaction behind |
| `AccountSharingPermissionTest` | Viewers can read but not write, editors can write, strangers get `403`, and only the owner manages sharing |
| `BudgetTest` | Budgets are stored per month and scoped to their owner |
| `AdminAccessTest` | The admin area is closed to regular users |
| `ReportTest` | Reports count only the user's own cleared transactions |
| `ReportDataTest` | The reports JSON: totals, category breakdown and zero-filled months in cents; filters; `422` for bad dates or unknown categories; a fixed query count for any range |
| `report.test.ts` / `ReportsApp.test.tsx` | Money formatting from cents, percentage shares, chart series; first paint from the server payload without a fetch, refetch on filter change, error with retry |

Writing these surfaced a real authorization hole: transaction creation checked nothing about the target account, so any user could post against another user's account id and change its balance. Both the create and update requests now require edit permission on the target account.

## Reports: React island

The rest of FinTrack is server-rendered Blade, and it stays that way. The Reports page is the one
screen where interactivity pays off, so it is a React + TypeScript component mounted into the Blade
layout rather than a rewrite of the app:

- **First paint without a spinner.** The Blade view embeds the initial report as JSON in a
  `data-initial` attribute, so React renders real numbers immediately.
- **Filters without reloads.** Changing a date or ticking a category calls `GET /reports/data`,
  a JSON endpoint behind the same session auth, validated by `ReportFilterRequest` (dates must be
  `Y-m-d`, the range must not run backwards, category ids must exist). A newer request aborts an
  older one, and the query string is kept in sync so the view stays bookmarkable.
- **Fewer queries.** The old controller issued two SQL queries per month plus one per category. The
  `ReportData` service computes totals, the category breakdown and the monthly trend in three grouped
  queries regardless of the range — a test pins the count.
- **Money stays in cents** through the API and is only divided by 100 when formatted.

## Roadmap

- [ ] CSV import/export
- [ ] Multi-currency support
- [ ] Recurring transactions generated automatically rather than flagged manually
