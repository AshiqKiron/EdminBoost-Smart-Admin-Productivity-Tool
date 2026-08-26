# EdminBoost QA Guide

Three-layer QA harness for local MAMP development.

## Prerequisites

- MAMP running (Apache + MySQL)
- MAMP PHP for PHPUnit: `/Applications/MAMP/bin/php/php8.3.28/bin/php`
- Node.js 18+

## One-time setup

```bash
# From plugin root
composer install
npm install
npx playwright install chromium
PLAYWRIGHT_BROWSERS_PATH=./.playwright-browsers npx playwright install chromium

cp .env.qa.example .env.qa
cp tests/wp-tests-config.sample.php tests/wp-tests-config.php

bash bin/setup-qa-site.sh
```

`bin/setup-qa-site.sh` creates QA users on your existing WordPress install (`http://localhost:8888/wordpress` by default) and symlinks the plugin.

## Run tests

```bash
# Backend (PHPUnit) — uses MAMP PHP + MySQL socket
composer test
# or: bash bin/run-phpunit.sh

# E2E (Playwright)
npm run test:e2e

# E2E debug UI
npm run test:e2e:ui

# E2E HTML report
npm run test:e2e:report
```

## Manual regression

Use [`tests/manual/regression-matrix.md`](manual/regression-matrix.md) after automated suites. Log issues with [`tests/manual/issue-template.md`](manual/issue-template.md).

## Environment variables (.env.qa)

| Variable | Default |
|----------|---------|
| `BASE_URL` | `http://localhost:8888/wordpress` |
| `ADMIN_USER` | `qaadmin` |
| `ADMIN_PASSWORD` | `qaadmin123` |
| `PLUGIN_SLUG` | `edminboost-smart-admin-productivity-tool` |

Override `WP_PATH` and `QA_URL` in `bin/setup-qa-site.sh` if your install differs.

## PHPUnit database

Tests use the `wordpress` database with table prefix `wptests_` (separate from live tables). Configure `tests/wp-tests-config.php` if your MAMP MySQL socket or credentials differ.
