---
name: blueprint
description: Before writing code in this project, interview the user and write ten planning notes (PRD, TRD, App Flow, UI/UX brief, Backend schema, API spec, Security matrix, Testing plan, Release plan, Implementation plan) to docs/blueprint/, then follow them during all development. For WordPress plugins, code follows the StorePress base plugin conventions.
---

# Project Blueprint

Plan first, code second. Before writing any code in this project, make sure a **blueprint** exists —
ten short notes describing what is being built and how — and treat it as the source of truth while coding.

This skill is installed **per project** at `.claude/skills/blueprint/`. Paths below are relative
to the project root; this skill's own files are under `.claude/skills/blueprint/`.

Blueprint location:

```
docs/blueprint/
  README.md                   Index, status of each note, decision log
  01-prd.md                   Product Requirements + risks & open questions
  02-trd.md                   Technical Requirements + compatibility & performance
  03-app-flow.md              User journey, every screen/link/button and where it goes
  04-ui-ux-brief.md           Design system + i18n & accessibility
  05-backend-schema.md        Storage, auth flow, tables, settings spec, data lifecycle
  06-api-spec.md              REST, AJAX, public hooks/filters, third-party APIs
  07-security-matrix.md       Capability, nonce, sanitize, escape per action; threats
  08-testing-plan.md          Acceptance criteria and test cases
  09-release-plan.md          Versioning, build, distribution, migrations
  10-implementation-plan.md   Step-by-step build sequence with checkboxes
```

## 0. Decide which mode you are in

Check for `docs/blueprint/README.md` first.

| Situation | What to do |
|---|---|
| No blueprint, and the request is a new project, plugin, app or a sizeable feature | Run the **Interview** (section 1), write all ten notes, then start coding at step 1 of the plan. |
| Blueprint exists | Skip the interview. Read the notes, then **Develop** (section 3). |
| Blueprint exists but the request is a new feature not in it | Run a **mini-interview** for that feature only, append it to every affected note, add steps to the plan, then develop. |
| No blueprint, request is a small fix (a typo, one function, a lint error) | Ask once: "Run the blueprint first, or just make this change?" Respect the answer. |

Never write production code before the blueprint is confirmed, unless the user explicitly says to skip it.

## 1. Interview

Go through the notes in order; each answer shapes the next note's questions. Per note:

1. **Ask** — 3–6 focused questions at a time (use the AskUserQuestion tool with options when available;
   offer sensible defaults so the user can just pick). Never dump every question at once, and never ask
   what an earlier note already answered.
2. **Fill gaps yourself** — where the user says "you decide" or skips, choose a sensible default and mark it `(assumed)`.
3. **Draft** the note from section 2 and show a short summary.
4. **Confirm** — "Anything to change before I move on to <next note>?" Apply edits, then write the file.

Notes 6–10 are mostly derived from notes 1–5: draft them yourself, then ask only about real gaps.

### 1.1 PRD — always first
Start with: **"What do you want to build?"** Then:
- Problem it solves; users and roles (admin, shop manager, customer, guest…).
- Core v1 features vs. later features. Explicitly out of scope.
- Success criteria: how we'll know it works.
- Constraints: deadline, budget, Free vs. Pro split, license, distribution (wordpress.org, private, SaaS).
- Competitors/references to resemble or differ from.
- **Risks & open questions:** unknowns, assumptions, who decides, by when.

**Then classify the project type** — this decides the TRD questions:
- **WordPress / WooCommerce plugin** → 1.2a
- Anything else (web app, API, CLI, mobile, Rust/PHP/Node service…) → 1.2b

### 1.2a TRD — WordPress plugin (StorePress defaults)
Pre-fill from the StorePress base plugin and ask the user to confirm or change; no open-ended stack questions.
- Scaffold: https://github.com/EmranAhmed/storepress-base-plugin (confirm).
- Plugin name, slug / text domain, root namespace (`StorePress\<Name>`), function prefix, constant prefix.
- Minimums: PHP 7.4, WordPress 6.4 (confirm or raise). WooCommerce required? (then HPOS compatible).
- Free/Pro split? (Pro path via `get_pro_plugin_file()`, `ProPluginInCompatibility`).
- Parts needed: settings page (AdminUtils `AbstractSettings`), blocks (dynamic / static / interactive),
  frontend JS plugins, REST endpoints, AJAX, cron, custom post types, admin menu, updater, deactivation feedback.
- Third-party APIs/services and their auth method.
- Tooling as in the base plugin: Composer (Jetpack autoloader, `storepress/admin-utils`),
  `@wordpress/scripts`, PHPCS (WPCS + WooCommerce + VIP), PHPStan max, PHPUnit, Jest, Playwright.
- **Compatibility & performance:** PHP/WP/WooCommerce version matrix, HPOS, multisite, known theme/plugin
  conflicts, caching (object cache, transients), query limits, asset size budget, lazy loading.

### 1.2b TRD — other projects
Propose a stack from the PRD and confirm: language/framework, database, hosting/deploy, auth provider,
external APIs, package manager, testing, lint/format, CI, environments (local/staging/prod).
- **Compatibility & performance:** supported browsers/devices/OS, response-time and load targets, caching.

### 1.3 App Flow
- Entry points (admin menu, block inserter, shortcode, frontend page, URL, CLI command…).
- For each role: the full journey from first touch to goal, step by step.
- Every screen/page; on each, every link, button and form and **where it goes / what it does**
  (success, error, empty state, permission denied).
- Notifications/emails, redirects, and what happens on deactivation/uninstall.
- Numbered flow plus a Mermaid `flowchart`.

### 1.4 UI/UX design brief
- Look & feel: WordPress admin (`@wordpress/components`) or custom brand? Light/dark?
- Fonts (family, sizes, weights), color palette (primary, secondary, neutral, success/warning/error with hex),
  spacing scale, radius, shadows.
- Components (buttons, inputs, tables, modals, notices, cards, tabs…) with states
  (default, hover, focus, disabled, loading, error, empty).
- Responsive breakpoints. For blocks: supports to expose (color, spacing, typography, border), editor vs. frontend.
- **i18n & accessibility:** text domain, translator comments for placeholders, RTL, WCAG 2.1 AA (default),
  keyboard navigation and focus order, screen-reader labels.

### 1.5 Backend schema
- What data is stored and where. For WordPress choose per entity and justify: options (Settings service),
  post/user/term meta, custom post type, custom table (`$wpdb` + `dbDelta`, versioned migrations),
  transients/object cache, WooCommerce order data (HPOS-safe CRUD).
- Each table/entity: columns with type, null/default, indexes, relationships (1–1, 1–n, n–n). Mermaid `erDiagram`.
- Authentication & authorization flow (WordPress capabilities and roles; for non-WP: provider, sessions/JWT, roles).
- **Settings spec:** every tab and field — `id`, `type`, `title`, `default`, `options`, `condition`, `show_in_rest`.
- **Data lifecycle:** validation/sanitization, retention, GDPR export/erase, what deactivation vs. `uninstall.php` removes.

### 1.6 API & integrations spec
- REST routes: method, path (`<namespace>/v1/...`), args with types/validation, response shape, error codes,
  `permission_callback`.
- AJAX actions: action name, nonce, capability, payload, response.
- Public hooks/filters this plugin **provides** (name, args, return, when fired, `@since`) — this is a public API, keep it stable.
- Hooks/filters it **uses** from WordPress, WooCommerce and others.
- Third-party APIs: endpoints, auth, rate limits, timeouts, retries, failure fallback.

### 1.7 Security & permissions matrix
One table row per action (form submit, AJAX, REST route, admin page, block render):
action · who can do it (capability) · nonce · input sanitize function · output escape function · notes.
Then a threat checklist: CSRF, XSS, SQL injection (`$wpdb->prepare`), privilege escalation, file upload,
direct file access (`ABSPATH` guard), secrets storage, SSRF on remote requests.

### 1.8 Testing plan
- Acceptance criteria for every PRD v1 feature (Given / When / Then).
- PHPUnit cases per service/feature (`tests/php/Unit/<mirror of includes>`), Jest cases per JS util/plugin,
  Playwright E2E cases per App Flow journey.
- Compatibility checks from the TRD matrix; manual QA checklist.

### 1.9 Release & deployment plan
- Versioning (semver), where the version lives (plugin header only), changelog format.
- Build & package: `npm run build`, package script, files excluded from the zip.
- Distribution: wordpress.org (readme.txt, assets, SVN) or private updater (`Updater` integration); Free/Pro packaging.
- Upgrade path: DB version option and migrations, backward compatibility, deprecations.
- Pre-release checklist: lint, PHPStan, tests, Plugin Check, i18n `.pot`, readme.

### 1.10 Implementation plan — you draft it, user approves
Derive it from notes 1–9. Ordered steps, each small enough for one session, each with goal, files to
create/change, verification (lint, PHPStan, tests from note 8, security rows from note 7), and a checkbox.
Typical WordPress order: scaffold/rename → container & service providers → settings → data layer/migrations →
features (hooks) → REST/AJAX → blocks/frontend JS → styles → tests → i18n/readme → release build.

## 2. Note format

Every note starts with:

```markdown
# <Note title> — <Project name>
Status: Draft | Confirmed   ·   Last updated: <YYYY-MM-DD>
```

Then headings matching the questions in section 1. Tables for fields, colors, columns, components, routes,
hooks and the security matrix. Bullets over prose; keep each note concise. Mark your own choices `(assumed)`.

`README.md` holds: a one-paragraph project summary, a table of the ten notes with status, key conventions
(for WordPress: "Follow StorePress base plugin conventions — see `.claude/skills/project-blueprint/SKILL.md` section 4"),
and a **Decision log** (date · decision · reason · notes affected).

`10-implementation-plan.md` uses checkboxes:

```markdown
- [ ] **Step 3 — Settings page.** Goal: … Files: `includes/Services/Settings.php` … Verify: `npm run lint:php`, `npm run stan:php`, settings tests from 08, rows S1–S3 from 07.
```

After writing the blueprint, add (or update) this in the project's `CLAUDE.md`:

```markdown
## Blueprint
Read `docs/blueprint/` before coding and follow it (skill: `project-blueprint`).
Work through `10-implementation-plan.md` in order.
```

## 3. Develop — following the blueprint

1. At the start of every session read `README.md`, `10-implementation-plan.md`, and the notes the next step touches.
   Find the first unchecked step.
2. Tell the user which step you're on, then implement **only that step**.
3. Code must match the notes: names, fields, columns, routes, hooks, flows, colors, components,
   and the capability/nonce/sanitize/escape from the security matrix.
4. Verify as the plan says (lint, static analysis, tests from the testing plan). Fix what fails.
5. Tick the checkbox and report: what was done, how it was verified, the next step.
6. **Deviation rule:** if the blueprint is wrong, impossible or missing something, stop and tell the user.
   After they agree, update the affected note(s), bump "Last updated", add a Decision log entry — then code.
   Never let code and blueprint silently drift apart.
7. New ideas mid-build go to the PRD "Later" list or a new plan step, not straight into code, unless the user says so.

## 4. WordPress plugins: StorePress conventions (mandatory)

For every WordPress plugin, code style, structure and conventions **always** follow
https://github.com/EmranAhmed/storepress-base-plugin.

- **New plugin:** clone the base plugin, then rename slug, text domain, namespace, function prefix and
  constants everywhere to the TRD values; update the plugin header and `composer.json`; run `npm install`.
  Copy this skill into the new project's `.claude/skills/project-blueprint/`.
- **Before writing any plugin file**, load the conventions, in this order of preference:
  1. The project's `.claude/skills/create-storepress-plugin/SKILL.md` (ships with the base plugin) —
     invoke that skill if listed, otherwise read the file and its `references/templates.md`.
  2. This skill's `references/conventions.md` and `references/templates.md`.
  3. If neither exists, fetch them from the repo on GitHub.
- Also read the project's `CLAUDE.md` and the closest existing sibling file, and copy its shape.
- Use the base plugin's other skills when they fit: `write-unittest` (note 8), `security-audit` (note 7),
  `write-phpdocs`, `fix-phpstan`, `fix-textdomain`.
- Key rules (full detail in the conventions file): bootstrap file only boots; `Plugin` lists service providers;
  every service is a singleton wired through its own ServiceProvider and resolved from the container;
  classes go in `Features/`, `Services/`, `Integrations/`, `ServiceProviders/`, `Traits/` by role;
  hook callbacks are `handle_*`; `array()` long syntax, Yoda conditions, strict types, full type hints and docblocks;
  every string translated with the plugin text domain; escape late; prefix all globals;
  blocks are folders under `src/` and auto-registered; never edit `build/`, `vendor/`, `languages/`.
- Verify after each change: `npm run lint:php`, `npm run stan:php`, `npm run lint:js`, `npm run lint:css`,
  and the tests from the plan. Never silence PHPCS/PHPStan when a real fix exists.

## Never
- Write production code before the blueprint is confirmed (unless the user says to skip).
- Ask all questions at once, or ask about things earlier notes already answered.
- Change scope, schema, API, permissions or stack without updating the blueprint and the decision log.
- Deviate from StorePress conventions in a WordPress plugin.
