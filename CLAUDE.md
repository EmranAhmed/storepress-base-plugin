# CLAUDE.md

WordPress plugin. Sample/base plugin for StorePress.

## Project

- **Entry file:** `storepress-base-plugin.php` — read its header for `Version`, `Text Domain`,
  `Requires PHP`, and `Requires at least`. Do not hardcode these elsewhere.
- **Namespace:** `StorePress\Base`
- **Minimum:** PHP 7.4, WordPress 6.4

## Layout

- `includes/`, `templates/` — PHP
- `src/` — JS, TS, SCSS sources
- `tests/` — tests, not linted

## Commands

- `npm run lint:php` — phpcs
- `npm run lint:js` / `lint:css` / `lint:pkg-json` / `lint:md:docs`
- `npm run stan:php:report` — PHPStan report (`stan:php:clear` clears its cache)

## Rules

- Follow WordPress Coding Standards; run the matching lint command on every file you change.
- Ask before modifying files outside the task.
