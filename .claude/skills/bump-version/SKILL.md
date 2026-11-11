---
name: bump-version
version: 1.0.0
last-updated: 2026-11-11
description: Bump a WordPress/WooCommerce plugin's version following SemVer and write the changelog. Picks major/minor/patch from what changed in the codebase since the last release, updates package.json, composer.json, the plugin entry file's Version header, and README.txt's Stable tag, then writes matching changelog entries into README.txt's == Changelog == section and changelog.txt in their existing format. Use whenever the user asks to bump, increase, release, or prepare a new version, update the changelog, or says "bump-version", even without naming the files.
---

# Bump Version (SemVer) + Changelog

Work out the right next version from what actually changed in the code, update the version everywhere it lives, and
write changelog entries that match the format the project already uses.

## Workflow

1. **Find the version files** in the plugin root (match file names case-insensitively: `README.txt` / `readme.txt`,
   `changelog.txt` / `CHANGELOG.txt`):
    - `package.json` — `"version"` field (and `package-lock.json` if present: top-level `"version"` and
      `packages[""].version`).
    - `composer.json` — `"version"` field, **only if it already exists**. Composer recommends omitting it, so never add
      it.
    - **Plugin entry file** — the root `*.php` file whose header block contains `Plugin Name:`. Update its ` * Version:`
      line.
    - `README.txt` — the `Stable tag:` line.
    - `changelog.txt`.
    - Also look in the entry file (and `includes/`) for a version constant or property whose value equals the current
      version, e.g. `define( 'MY_PLUGIN_VERSION', '1.4.2' );` or `const VERSION = '1.4.2';`. Update it too so the plugin
      doesn't report a stale version, and say so in the summary.
    - If a file doesn't exist, skip it and mention it in the summary. Don't create missing files except `changelog.txt`
      if the user asks.

2. **Read the current version.** The plugin header `Version:` is the source of truth. Compare it with every other
   location. If they disagree, list the mismatches and ask which is correct before continuing.

3. **Collect the changes since the last release.**
    - Find the last release point, in this order:
        1. A git tag matching the current version: `git tag --list "v1.4.2" "1.4.2"`.
        2. The commit that set the current version: `git log -1 --format=%H -S "Stable tag: 1.4.2" -- README.txt` (or
           `-S "Version: 1.4.2"` on the entry file).
        3. If neither exists, ask the user for the base commit or tag.
    - Gather: `git log --no-merges --format="%h %s%n%b" <base>..HEAD`, `git diff --stat <base>..HEAD`, and
      `git diff <base>..HEAD`.
    - Include uncommitted work: `git status --porcelain` and `git diff HEAD`. Tell the user if uncommitted changes are
      part of the release.
    - **Read the diff itself**, not just commit messages. Commit messages are hints; the code decides.
    - Ignore version-only edits from the files above and noise (lock files, build output, `build/`, `vendor/`, `node_modules/`,
      minified assets) when classifying, unless they change behavior.
    - Group the changes as Breaking, Added, Changed/Improved, Fixed, Security, Dev/Tooling.

4. **Decide the bump** using the rules below. If the user named the bump type (`major`, `minor`, `patch`) or an exact
   version, use theirs and just note it if the code suggests a different level.

5. **Draft the changelog entries** (see "Writing the changelog").

6. **Confirm when it matters.** If the result is a **MAJOR** bump, or the evidence sits between two levels, show the
   proposed version, the reason, and the draft changelog, and wait for a yes. For clear MINOR/PATCH bumps, go ahead and
   write.

7. **Apply the edits** (see "Editing rules").

8. **Verify** (see "Verification"), then report.

## Choosing MAJOR / MINOR / PATCH

Given current version `X.Y.Z`:

### MAJOR → `(X+1).0.0` — anything that can break existing sites, themes, or integrations

- A public PHP function, class, method, or constant is **removed or renamed**, or its signature changes incompatibly
  (required parameter added, parameter removed/reordered, return type changed).
- An action or filter **hook is removed or renamed**, or its arguments change order/meaning.
- A shortcode, block, REST route, AJAX action, or template file is removed or renamed, or its output/response shape
  changes incompatibly.
- Template overrides in themes would break (template file moved, required variables removed).
- Stored data changes in a way that isn't migrated automatically or can't be read by the old version (option/meta key
  renamed without migration, custom table restructured).
- Minimum **PHP, WordPress, or WooCommerce** requirement raised (`Requires PHP`, `Requires at least`,
  `WC requires at least`) — sites on the old version can no longer run it. Treat as MAJOR unless the project's existing
  changelog shows it handles these as minor; if so, follow the project and mention it.
- A setting's default changes so that existing behavior silently changes for current users.
- Commit signals: `BREAKING CHANGE:` footer, `!` after the type (`feat!:`).

### MINOR → `X.(Y+1).0` — new, backward-compatible functionality

- New feature, setting, admin screen, shortcode, block, widget, REST route, AJAX action, or integration.
- New public function, class, method, hook, or filter.
- New optional parameter with a default that keeps old behavior.
- Something deprecated (with `_deprecated_function()`, `_deprecated_hook()`, etc.) but still working.
- New compatibility (e.g. supports HPOS, a new gateway).
- Commit signals: `feat:`.

### PATCH → `X.Y.(Z+1)` — backward-compatible fixes only

- Bug fixes, security fixes, performance improvements with no API change.
- Text, translation, styling, and accessibility fixes.
- Tests, refactoring with no behavior change, dependency patch updates.
- `Tested up to` / `WC tested up to` bumps.
- Commit signals: `fix:`, `perf:`, `refactor:`, `chore:`, `docs:`, `style:`, `test:`.

### Special cases

- **Highest level wins.** One breaking change plus ten fixes is MAJOR.
- **Pre-1.0 (`0.y.z`):** breaking changes bump MINOR (`0.(y+1).0`), everything else bumps PATCH.
- **Pre-release current version** (`2.0.0-beta.2`): ask whether to cut the final release (`2.0.0`) or the next
  pre-release (`2.0.0-beta.3`).
- **Nothing user-facing changed** (only tests/CI/docs): say so and ask whether a release is wanted; if yes, PATCH.
- When a bump resets lower numbers, reset them to 0 (`1.4.2` → MINOR → `1.5.0`, → MAJOR → `2.0.0`).

Always state the reason in one line, citing the deciding change, e.g. "MINOR: new `sp_after_checkout` filter in
`includes/class-checkout.php`; no breaking changes."

## Writing the changelog

### Match the existing structure exactly

Before writing, read the most recent 2–3 entries in **both** README.txt's `== Changelog ==` section and `changelog.txt`.
Copy, for each file separately:

- **Entry heading format**, e.g. `= 1.5.0 =`, `= 1.5.0 - 2026-11-11 =`, `= 1.5.0 (2026-11-11) =`,
  `*** 2026-11-11 - version 1.5.0 ***`, `## [1.5.0] - 2026-11-11`.
- **Date format** (`2026-11-11`, `11/11/2026`, `November 11, 2026`, `2026.11.11`). Use today's date.
- **Line prefix style**, e.g. `* Fix - `, `* Add - `, `* Tweak - `, `* Dev - `, `* Fixed: `, `- `, `* [Fix] `.
- **Category names and order**, if the project groups or sorts by type.
- **Ordering** — almost always newest at the top, directly under the section heading (`== Changelog ==` in the readme,
  or under any title line in `changelog.txt`).
- **Spacing** — blank lines between entries, indentation, trailing newline.
- **Retention** — some readmes keep only the last few versions and point to `changelog.txt` for the rest. If so, keep
  the same number of entries in the readme (drop the oldest) and keep the pointer line.

The two files may use different formats; follow each one's own. If a file has no prior entries, use the WordPress style
for README.txt (`= 1.5.0 - 2026-11-11 =` then `* Add - ...`) and the WooCommerce style for changelog.txt
(`*** Plugin Name Changelog ***`, then `2026-11-11 - version 1.5.0` and `* Add - ...` lines).

### Write good entries

- One line per user-visible change, written for site owners, not developers' commit history: "Fix - Coupon discount not
  applied when cart contains subscription products." not "fix bug in calc()".
- Group related commits into a single entry; skip pure noise (typos in comments, CI tweaks, merge commits).
- Order within the entry: breaking changes first, then additions, then fixes, then tweaks/dev notes — unless the
  existing format orders differently.
- Mark breaking changes clearly (e.g.
  `* Breaking - Removed the deprecated sp_get_items() function; use sp_query_items().`) and add an
  `== Upgrade Notice ==` entry in README.txt if that section exists.
- Developer-facing changes (new hooks, deprecations) get a `Dev -` (or the project's equivalent) line naming the
  hook/function.
- **Security fixes:** describe the area fixed without exploit details, e.g. "Security - Improved permission checks on
  the order export endpoint."
- Requirement changes get their own line, e.g. "Update - Requires PHP 7.4 or higher."

## Editing rules

Use targeted edits, never rewrite whole files. Preserve indentation, quotes, line endings, and key order.

- `package.json` / `composer.json`: replace only the value of the top-level `"version"` key.
- `package-lock.json`: update the top-level `"version"` and `"packages"[""]["version"]` only — never dependency
  versions.
- Plugin header: replace only the version on the `Version:` line inside the header comment, keeping its spacing (e.g.
  ` * Version:           1.5.0`).
- README.txt: replace only the `Stable tag:` value. Don't touch `Tested up to`, `Requires at least`, `Requires PHP`, or
  `WC tested up to` unless the user asks.
- Version constant/property: replace only the string literal.
- Don't change the version anywhere else (e.g. `@since` tags, asset query strings built from the constant).
- Don't commit, tag, or push unless the user asks. If asked, commit with a message like `Release 1.5.0` and tag in the
  existing tag style (`v1.5.0` or `1.5.0` — match previous tags).

## Verification

Run these and fix anything that fails before reporting:

```bash
# No old version left in the version locations
grep -rn "1.4.2" package.json composer.json README.txt changelog.txt <entry-file>.php

# New version present everywhere
grep -rn "1.5.0" package.json composer.json README.txt changelog.txt <entry-file>.php

# JSON still valid
php -r 'foreach (["package.json","composer.json","package-lock.json"] as $f) { if (is_file($f)) { json_decode(file_get_contents($f)); echo $f, ": ", json_last_error_msg(), PHP_EOL; } }'

# Entry file still parses
php -l <entry-file>.php

# Review the full diff
git diff
```

A remaining old version string is fine only if it's inside an older changelog entry or an `@since` tag.

## Report to the user

Keep it short:

- `1.4.2 → 1.5.0 (MINOR)` and the one-line reason.
- Files updated (and any skipped because they don't exist or have no version field).
- The new changelog entry as written.
- Anything uncertain (mismatched versions found, uncommitted changes included, a borderline major/minor call).
