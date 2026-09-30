---
name: Write PHPDocs
description: Add or update PHP DocBlocks in any WordPress or WooCommerce plugin or theme, with types precise enough for PHPStan and IDE autocompletion. Covers file, class, method, property, and hook documentation, @since versions taken from git history, and translators comments. Documentation only — never changes code behaviour. Use whenever the user asks to write, add, fix, or update PHPDoc, docblocks, or code comments, document hooks, add @since tags, or resolve phpcs commenting errors.
---

# Write PHPDocs — WordPress / WooCommerce

Portable: no plugin, namespace, PHP version, or tool configuration is assumed. Everything is
detected in step 0.

## 0. Profile the project

- **Plugin version**: the `Version:` header of the main plugin file (or the theme's `style.css`) —
  this is the `@since` value (step 3).
- **PHP version**: the `Requires PHP:` plugin header or `require.php` in `composer.json`.
- **Standards**: `phpcs.xml` / `phpcs.xml.dist` (which sniffs), `phpstan.neon` (which level).
- **Local dialect**: read a few well-documented files and copy their conventions — tag order,
  `@package` name, whether `@return void` is written, how array shapes are typed, existing
  `@phpstan-type` aliases, section-header style.

## 1. Hard rules

- **Documentation only.** Add or update DocBlocks and comments. Never change code: no renames, no
  signature or type-hint changes, no `declare( strict_types=1 )`, no fixes.
- **Types come from the code, not the name.** Read the body and the callers. If the code and the
  existing docblock disagree, document what the code does and report the mismatch.
- **Keep it concise.** One summary line is ideal; add a description only when the *why* is not
  obvious. Do not restate the function name or narrate the body.
- **Never invent a `@since` version** (see step 3). Keep the ones that are there.
- Spotted a bug, a missing strict-types declaration, or a badly named method? List it in the
  report — do not touch it.

## 2. What to document

**File** — summary and `@package`. Add other tags only if the project already uses them.

**Class / interface / trait** — summary and `@since`. Use `@method` and `@property` only for magic
members, `@template` for generics, and `@phpstan-type` for an array shape used in 3+ places.

**Method / function** — summary in the third person ending with a period ("Gets the order
total."), then `@since`, `@param`, `@return`, and `@throws` when it throws. Add `@see` for closely
related methods.

**Property / constant** — summary and `@var`.

**Inline `@var`** — always a full doc comment whose summary says why the type is known:

```php
/**
 * The API client returns every response as an untyped array; this endpoint has this shape.
 *
 * @var array{id: int, status: string} $response
 */
```

**Types** — as precise as the code allows, so PHPStan and IDEs can use them: `list<string>`,
`array<string, int>`, `array{id: int, name?: string}`, `class-string<T>`, `int|false`,
`\WP_Error|array`. Never bare `array` or `mixed` when the shape is knowable.

## 3. Finding `@since`

Read the `Version:` header of the main plugin file (the PHP file with a `Plugin Name:` header), or
of `style.css` for a theme, and use that value for every `@since` you add. Read it once per run.

Existing `@since` tags are never changed. If no `Version:` header is found, ask.

## 4. Hooks and translations

**`do_action()` / `apply_filters()`** — a DocBlock directly above: when it fires or what it
filters, `@since`, and one `@param` per argument passed.

```php
/**
 * Fires after an order has been synced.
 *
 * @since 1.2.0
 *
 * @param int    $order_id Order ID.
 * @param string $status   New sync status.
 */
do_action( 'prefix_order_synced', $order_id, $status );
```

A hook fired in more than one place is documented once; the others get
`/** This action is documented in path/to/file.php */`.

**`add_action()` / `add_filter()`** — a one-line inline comment, only where the purpose is not
obvious from the callback name.

**Translations** — a `/* translators: %s: … */` comment directly above every i18n call that
contains a placeholder.

## 5. Method organization (optional — ask first)

Reordering moves code and makes the diff large, so offer it as a separate step and do it only when
the user agrees. Group methods under section headers named after what the class actually contains
(lifecycle, hooks, getters, setters, helpers), in the project's header style or:

```php
// =====================================================================
// Setters
// =====================================================================
```

Move whole methods only; never edit inside them.

## 6. Verify and report

1. `git diff` shows comment lines only (plus moved methods if step 5 was approved).
2. The project's linter are clean for every changed file.
3. PHPStan, if configured, reports no more errors than before.
4. Report per file: what was documented, any `@since` that could not be determined, and a separate
   list of code issues spotted but not touched.
