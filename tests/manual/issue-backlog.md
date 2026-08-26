# EdminBoost QA Issue Backlog

Generated from first full automated QA run (PHPUnit + Playwright) on MAMP WordPress 7.1.

## Fixed during QA setup (P0 / P1)

| ID | Severity | Area | Issue | Fix |
|----|----------|------|-------|-----|
| QA-FIX-001 | P1 | Layout Studio drawer preview | AJAX preview always failed because `is_mapper_screen()` checked `$_GET['page']`, which is absent on `admin-ajax.php` | Added `is_mapper_preview_context()` with referer validation; updated `ajax_drawer_preview()` |
| QA-FIX-002 | P2 | Layout Studio PHP | Discovered list `is-active` used slug only, ignoring anchor | `$active_slugs` now keys slug + anchor; discovered rows match `slug + "\0"` |

## Open / manual verification (P2)

| ID | Severity | Layer | Area | Notes |
|----|----------|-------|------|-------|
| QA-005 | P2 | Manual | Hide notices | Verify third-party plugins using non-`.notice` markup |
| QA-006 | P2 | Manual | Responsive UI | Mapper sidebar/drawer at 960px and 782px breakpoints |
| QA-007 | P2 | Manual | Badge sources | WC / WPForms badge counts with plugins installed |
| QA-008 | P2 | Manual | Front-end drawer | Drawer from public site with admin bar showing |
| QA-009 | P2 | Manual | Cross-plugin | Discovery list completeness with WooCommerce / heavy menus |

## Automated coverage summary

| Layer | Result |
|-------|--------|
| PHPUnit | 31 tests, 49 assertions — **PASS** |
| Playwright E2E | 15 tests — **PASS** |
| Manual matrix | Run [`regression-matrix.md`](regression-matrix.md) before release |

## How to re-run and update this backlog

```bash
composer test
npm run test:e2e
```

Triage failures into this file using [`issue-template.md`](issue-template.md).
