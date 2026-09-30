---
name: Fix PHPStan
description: Fix PHPStan errors in any WordPress or WooCommerce plugin, at any level. Profiles the project first, then fixes producer files before consumer files, always updating PHPDoc and preferring honest runtime guards over silencing annotations. Asks for approval and shows the planned change before modifying any file. Never a baseline, an ignoreErrors entry, or a level drop. Use whenever the user asks to fix PHPStan, run static analysis, raise the PHPStan level, or mentions `stan`, a PHPStan report, or PHP type errors.
---

# Fix PHPStan — WordPress / WooCommerce plugins

Portable: no plugin, namespace, file path, or script name is assumed. Everything project-specific is
detected in step 0.

## The one idea

WordPress core is loosely typed on purpose (`get_option()` is `mixed`, `wp_json_encode()` is
`string|false`), and one loose value contaminates everything downstream. Every error is one of three
things:

1. **A real hole** — core can return `false`/`null`/the wrong type and the code does not handle it.
   Add a guard. This is the majority.
2. **Undeclared knowledge** — the value cannot be wrong, but PHPStan cannot see why (SDK typed
   `mixed`, a `global`, an `include`d file). Add PHPDoc that states why.
3. **A real bug** — the code is wrong today. Fix it and report it loudly.

Deciding which one you are looking at is the whole job. Step 3 is how.

## 0. Profile the project

Detect, do not assume:

- **Config and level**: `phpstan.neon` / `phpstan.neon.dist`.
- **Run commands**: scripts in `composer.json` or `package.json` (analyse, report, clear cache).
  If none exist, use `./vendor/bin/phpstan analyse --memory-limit=2G`.
- **Linter**: `phpcs.xml` / `phpcs.xml.dist` and the script that runs it.
- **Local dialect**: `array()` vs `[]`, how existing docblocks write types, existing
  `@phpstan-type` aliases, and any project helpers for sanitizing or reading settings.

## 1. Run and plan

Run the project's report command. If the result looks stale after a config or stub change, clear the
cache and re-run. Group the errors by file, show the counts, and mark each file as producer or
consumer:

```
47 errors across 9 files

  <producer file> ........ 18   <- do first
  <consumer file> ........ 12   <- consumes the producer, will shrink on its own
  <template file> ........  9
```

**Fix producer files first, then consumer files.** A producer is a file whose return values other
files consume (services, API clients, models, settings getters). Consumers are its callers,
including templates. Order by data flow, not by error count: typing a producer clears many consumer
errors for free, and going the other way means writing the same array shape several times and then
reconciling them. Re-run the full report after each producer before planning its consumers.

**A level raise is a scope change.** Fix at the current level first; offer a raise as a separate
step. Never raise it silently, never lower it.

## 2. The fix loop — one file at a time

1. Read the file's errors. If the cause is unclear, do not guess: add
   `\PHPStan\dumpType( $value );` and re-run. Whichever value comes back as bare `array`, `mixed`,
   or `array<int|string, mixed>` is the leak — fix there.
2. Pick the fix for each error with step 3.
3. **Ask before modifying.** Show the file, its error count, and every planned change as
   before → after (or a diff), each labeled *PHPDoc*, *guard / signature change*, or *bug fix*.
   Wait for approval. This applies to every file, including a file with no errors that must change
   because a return type was widened elsewhere.
4. Apply the approved changes only.
5. Re-run PHPStan scoped to that file, then the project's linter on it. A PHPStan fix that breaks
   phpcs is not finished.
6. Move to the next file.

Never batch-edit several files and run once — one wrong annotation cascades.

## 3. Decision rule — pick the fix from the source of the value

| The value came from…                                   | Fix                                                             |
|--------------------------------------------------------|-----------------------------------------------------------------|
| A core function that returns `false`/`null` on failure | guard + typed fallback                                          |
| `get_post_meta()` / `get_option()` / a settings getter | `is_numeric()` / `is_string()` + typed default, at the boundary |
| `$_POST` / `$_GET` / `$_REQUEST` / `$_FILES`           | `isset() && is_string()`, then `wp_unslash`, then sanitize      |
| A third-party SDK or API client typed `mixed`          | inline `@var` array shape, including the absent case            |
| A type error that describes a real defect              | fix the defect, report it as a behaviour change                 |
| Our own method that can genuinely fail                 | widen the signature to `…\|WP_Error` and propagate              |
| The same array shape in 3+ places                      | `@phpstan-type` alias on the class docblock                     |
| A helper that takes its argument by reference          | re-assert after the call, pass an explicit default              |
| A WP hook / filter callback signature                  | match WP's real contract (the `$post` arg, the `false` case)    |
| A `global`, or an `include`d asset manifest            | inline `@var`, with `\|null`                                    |
| A helper that reshapes rather than transforms          | `@template`                                                     |
| `$template_data` inside a template file                | `??` default per key + a shape mirroring the producer           |
| A `static` local singleton                             | explicit `null ===` check, not `??=`                            |
| Code that exists only to create the loose value        | delete it                                                       |

**Tie-breaker, when a guard and an annotation both silence the error:** can you write one true
sentence explaining why the value cannot be wrong? If yes, annotate, and that sentence is the
docblock summary. If not, the value can be wrong — guard it. Never paper over a real `false` case
with `@var`.

## 4. PHPDoc rules

**Updating PHPDoc is always recommended.** Whenever a function is touched, bring its `@param`,
`@return`, and `@throws` in line with the real types — precise array shapes and generics instead of
bare `array`, and the `false` / `null` / `WP_Error` cases included. Precise PHPDoc on a producer is
the cheapest fix there is for its consumers. Keep existing `@since` tags and never invent one.

**Every `@var` is a full doc comment with a summary line**, so the linter passes. The summary says
*why* the type is known, not what it is:

```php
/**
 * The SDK types every response as a generic result; this call always returns this shape.
 *
 * @var array{Buckets?: list<array{Name: string}>} $result
 */
$result = $client->list();
```

Not `/** @var array $result */` — no summary fails phpcs, and bare `array` fixes nothing.

## 5. Hard rules

- **No file is modified without approval** (step 2.3).
- **No `ignoreErrors`**, by message or identifier, and no baseline, unless the user asks explicitly.
- **No level drop**, for the whole config or for one file.
- **No `assert()`** as a narrowing device.
- **`@phpstan-ignore` is the last resort** — one or two in a whole plugin, each justified in the report.
- **No drive-by reformatting.** If a line had no error and no stale PHPDoc, do not touch it.
- **No unrelated improvements riding along.** List them separately instead.

## 6. Done criteria, per file

- [ ] Scoped PHPStan run is clean.
- [ ] Linter is clean: every new docblock has a summary line, and no restricted function crept in
  via a fallback (`date()` instead of `gmdate()` is the classic one).
- [ ] Every guard has a typed fallback of the right type (`''`, `0`, `array()`) — not `null`.
- [ ] Every widened return type is handled at every call site, templates included.
- [ ] PHPDoc of every touched function matches its real types, in the local dialect.
- [ ] No `dumpType()` left behind.
- [ ] The diff contains only approved changes.

## 7. Report back

Per file: the count delta, then one line per fix in three buckets.

```
<file> — 18 -> 0

  bugs found                     [behaviour changes — review these first]
    method()    what was wrong -> what it does now

  guards / signature changes     [code changes — review these]
    method()    return widened to array|WP_Error + try/catch     [contract change]

  PHPDoc
    method()    @var / @param / @return added or tightened, and why the type is known
```

End with the total, any files still failing, and unrelated issues spotted but not touched.
