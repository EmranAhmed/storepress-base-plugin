---
name: Fix Textdomain
description: Make hardcoded user-facing strings translatable in any WordPress plugin or theme. Detects the project's text domain and code style, wraps strings in the correct i18n function with the correct escaping, converts interpolated variables to sprintf placeholders with translators comments, handles plurals and strings containing HTML, and fixes wrong or missing text domains on existing calls. Changes strings only, never logic. Use whenever the user asks to fix the textdomain, add or fix i18n / l10n / translations, make strings translatable, prepare a .pot file, or resolve WordPress.WP.I18n phpcs errors.
---

# Fix Textdomain — WordPress plugins and themes

Portable: no plugin name, path, or text domain is assumed. Everything is detected in step 0.

## 0. Profile the project

- **Text domain**: read the `Text Domain:` header of the main plugin file (the PHP file with a
  `Plugin Name:` header) or the theme's `style.css`. Cross-check against the `text_domain` property
  in `phpcs.xml` / `phpcs.xml.dist` and against existing i18n calls. If they disagree or none is
  found, ask — do not guess.
- **Code style**: copy the spacing and quoting of existing i18n calls, e.g.
  `__( 'Text', 'domain' )` vs `__('Text', 'domain')`.
- **Linter**: note the phpcs script if there is one.

## 1. What to convert

**Convert** user-facing strings: `echo` / `print`, returned messages, exception messages,
`wp_die()`, admin notices, JSON / REST responses, and array values for labels, titles, messages,
and descriptions.

**Also fix** existing i18n calls with a missing or wrong text domain, or with a variable or constant
as the domain.

**Skip** empty strings, array keys, hook names, `define()` constants, log and debug messages, and
technical strings: option and meta keys, DB tables, SQL, slugs, CSS classes, regex, URLs, file
paths, and any value that code compares against.

## 2. Pick the function

| The string is…                                | Use                                          |
|-----------------------------------------------|----------------------------------------------|
| Echoed as text                                | `esc_html_e()` or `echo esc_html__()`        |
| Echoed inside an HTML attribute               | `esc_attr_e()` or `echo esc_attr__()`        |
| Returned or stored, and escaped later         | `__()` — escaping twice corrupts the text    |
| Returned or passed on with no later escaping  | `esc_html__()`                               |
| In an exception or `wp_die()`                 | `esc_html__()`                               |
| Singular / plural by a count                  | `_n()`                                       |
| Short and ambiguous ("Post", "Order", "Back") | `_x()` / `esc_html_x()` with a context       |

## 3. Special cases

**Variables** — replace interpolation and concatenation with `sprintf` and a placeholder. Use
numbered placeholders (`%1$s`, `%2$s`) when there is more than one.

```php
// Before: "Hello {$name}, welcome!"
sprintf(
	/* translators: %s: User name. */
	esc_html__( 'Hello %s, welcome!', 'domain' ),
	esc_html( $name )
)
```

**Plurals**

```php
sprintf(
	/* translators: %d: Number of items. */
	esc_html( _n( '%d item', '%d items', $count, 'domain' ) ),
	absint( $count )
)
```

**Strings containing HTML** — keep the sentence whole so it can be translated in any word order;
do not split it into fragments. Pass only the URL or value in, and escape the result with `wp_kses`.

```php
wp_kses(
	sprintf(
		/* translators: %s: Settings page URL. */
		__( 'Open the <a href="%s">settings page</a>.', 'domain' ),
		esc_url( $url )
	),
	array( 'a' => array( 'href' => array() ) )
)
```

## 4. Rules

- The text domain is always a string literal — never a variable, constant, or function call.
- Every string with a placeholder gets a `/* translators: … */` comment on the line directly above
  the i18n call, describing each placeholder.
- Never put a variable, function call, or concatenation inside the translatable string itself.
- Do not change the English wording, the logic, or any non-string code.
- Preserve formatting and indentation; touch only the lines that contain a converted string.
- Convert a string that appears in several files the same way every time.

## 5. Verify and report

1. Run `php -l` on every changed file, then the project's linter if it has one.
2. Report per file: the number of strings converted, the number of existing calls fixed, and any
   string left alone because it was unclear whether it is user-facing — list those so the user can
   decide.
