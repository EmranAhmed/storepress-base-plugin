---
name: write-jsdocs
version: 1.0.0
last-updated: 2026-11-17
description: Add or update JSDoc comments in the JavaScript source of any WordPress or WooCommerce plugin or theme. Groups imports under "External dependencies", "WordPress dependencies" and "Internal dependencies" headers, and types localized globals from the matching wp_localize_script() / wp_add_inline_script() calls in PHP. Documentation only — never changes code behaviour. Use whenever the user asks to write, add, fix, or update JSDoc, JS docblocks, or JS code comments, or to resolve eslint jsdoc/* errors.
---

# Write JSDocs — WordPress / WooCommerce

Portable: no plugin, namespace, build tool or lint configuration is assumed. Everything is
detected in step 0.

## 0. Profile the project

- **Source files**: the JS entry points, usually `src/**/*.js` (skip `build/`, `node_modules/`,
  `vendor/`). Confirm with `webpack.config.js` or the `scripts` in `package.json`.
- **Linter**: `@wordpress/scripts` (`npx wp-scripts lint-js <path>`) or the project's own
  `eslint` config. Its `jsdoc/*` rules decide which types and tags are accepted.
- **Plugin version**: the `Version:` header of the main plugin file. Only used for `@since`, and
  only if the project's JS already uses `@since` (step 5).
- **Local dialect**: read the best-documented JS file and the user's recent edits, and copy their
  conventions — whether `@file` is used, `@return {void}`, `@memberof`, British or American
  spelling. If the user edits a file you documented, treat their version as the style guide.

## 1. Hard rules

- **Documentation only.** Add, update or remove comments. Never change code: no renames, no
  reformatting, no fixed bugs, no moved statements — including import statements (step 2).
- **Types come from the code, not the name.** Read the body, the callers and the PHP that sends
  the data (step 3). If the code and an existing comment disagree, document what the code does.
- **Describe behavior, not syntax.** Say what a function does and why, in plain words. Don't
  narrate each line or restate the function name.
- **Remove wrong comments.** Docblocks copied from another project (for example WordPress core's
  `updates.js` with `@since 4.2.0`) describe that project, not this one: rewrite them and drop the
  foreign `@since` tags. Remove empty `//` lines and stray docblocks that document nothing.
- **Leave commented-out code alone.** It is code the author chose to keep; mention it in the
  report if it looks stale.
- Spotted a bug? List it in the report with the line and a suggested fix — do not touch it.

## 2. Dependency headers

Every import belongs to one of three groups, in this order, each under its own header comment:

```js
/**
 * External dependencies
 */
import jQuery from 'jquery';
import StorePressTooltip from '@storepress/tooltip';

/**
 * WordPress dependencies
 */
import {__} from '@wordpress/i18n';
import {speak} from '@wordpress/a11y';

/**
 * Internal dependencies
 */
import {formatPrice} from './utils';
import './style.scss';
```

| Import source                                                                                      | Group                  |
|----------------------------------------------------------------------------------------------------|------------------------|
| `@wordpress/*`                                                                                     | WordPress dependencies |
| Any other package name (`jquery`, `lodash`, `@woocommerce/*`, `@vendor/*`)                         | External dependencies  |
| Relative paths (`./`, `../`) and project aliases (e.g. `@/`, as set in webpack or `jsconfig.json`) | Internal dependencies  |

- Add a missing header; fix a header with the wrong name; drop a header whose group is empty.
- The header sits directly above its imports. Nothing goes between a header and its imports — a
  stray docblock there (e.g. `@member SOME_GLOBAL`) is moved into the main docs or removed.
- **Never reorder imports.** If groups are interleaved, add the headers you can without moving
  anything and report the order. Imports can have side effects, so reordering is the user's call.
- `jquery` is External even though the build maps it to `window.jQuery`.

## 3. Localized globals from PHP

Scripts often read data that PHP puts on `window`. Find the PHP side before typing it — never
guess property names from how the JS uses them.

1. **Find the global name in the JS** — the arguments passed into the IIFE (`window.MY_PLUGIN_PARAMS`), `window.X` reads, or `/* global X */` comments.
2. **Find where PHP sets it.** Search `*.php` outside `vendor/`:
    - `wp_localize_script( $handle, 'NAME', $data )` — the name is literal.
    - A wrapper that builds the name. Search for `wp_localize_script` itself, then read the helper
      around it. Example: `get_package_l10_object_name( 'settings' )` returns
      `STOREPRESS_ADMIN_UTILS_SETTINGS_PARAMS`, so the data is the `$l10` array passed to
      `register_package_scripts( 'settings', $l10 )` or `enqueue_package_scripts( 'settings', $l10 )`.
      Search for the script's file name as the first argument to find the caller.
    - `wp_add_inline_script( $handle, sprintf( 'Fn(%s)', wp_json_encode( $options ) ) )` — the data
      is passed as a function argument instead of a global; the `$options` array is the type.
3. **Type it** with a `@typedef` near the top of the file. One `@property` per key in the PHP
   array, typed from the PHP value (`__()` → `string`, `wp_create_nonce()` → `string`,
   `(bool)` → `boolean`). Note keys merged in from another method (e.g. `wp_parse_args( …,
   $this->get_localize_strings() )`) in the description, and mark keys the JS never reads ("Not used by this script.").

```js
/**
 * Localized strings passed from PHP via `wp_localize_script()`.
 *
 * @typedef {Object} SettingsParams
 * @property {string} unsaved_warning_text Message shown when leaving the page with unsaved changes.
 * @property {string} reset_warning_text   Confirmation message shown before resetting settings.
 */
```

If no PHP source is found, write the typedef from the JS reads and say so in the report.

## 4. What to document

**File** — a short header block above the imports: what the script does and where it runs. Name
the public API it exposes (`window.StorePress.AdminDialog()`) and any script it depends on at
runtime (`dialog.js`, `wp-util`). Use `@file` only if the project already does.

**IIFE wrapper** — a docblock with one `@param` per argument, typed with the typedefs from step 3.
Use real types: `{Window}`, not `{window}`; never `{window.SOME_GLOBAL}`.

**Object literal of methods** (`const Plugin = { … }`) — `@namespace Plugin` on the object, and
`@memberof Plugin` on each method if the file uses it.

**Function / method** — a summary in the third person ending with a period, a longer description
when the behavior is not obvious (which elements it reads by selector or `data-` attribute, what
it toggles, when listeners are removed), then `@param`, `@return`, `@throws`, `@fires`.

- Destructured parameters: document the whole object with a typedef, not each property.
- Callbacks passed as options: a `@callback` typedef, referenced from the options typedef.
- Public helpers: add an `@example` showing the call.
- Arrow functions assigned as handlers: explain why it is an arrow function if it matters (same
  reference for `addEventListener` / `removeEventListener`).
- Overrides of another library's methods (e.g. `wp.updates.*`): say "Overrides the core method"
  and what changed.
- `$( document ).trigger( 'name' )` / custom events: `@fires document#name`.
- Links to packages: `@see {@link https://www.npmjs.com/package/name}`.

**Inline comments** — only where the *why* is not obvious ("selectWoo fires jQuery `change`, not
native `input`"). Replace vague ones like `// Recommended`.

## 5. Types and tags the linter accepts

The `@wordpress/eslint-plugin` jsdoc rules reject some common types. Use:

| Instead of                 | Use                                             |
|----------------------------|-------------------------------------------------|
| `NodeListOf<Element>`      | `NodeList`                                      |
| `{window}` / `{window.wp}` | `{Window}` / `{Object}`                         |
| `{$.promise}`              | `{jQuery.Deferred}`                             |
| `{Function=}`              | `[name]` in `@property` / `@param` for optional |

- Align `@param` / `@property` columns; `wp-scripts lint-js --fix <files>` fixes alignment.
- `@since`: only if the project's JS already uses it, with the plugin version from step 0. Never
  invent one; keep correct existing ones.

## 6. Verify and report

1. Run the linter on every changed file and fix any `jsdoc/*` errors (comment changes only).
   Running `--fix` on a directory can touch unrelated files — pass file paths.
2. Check the diff shows only comment lines changed.
3. Report per file: what was documented, where each localized global's data came from (PHP file
   and line), anything you could not determine, and a separate list of bugs or stale code spotted
   but not touched.
