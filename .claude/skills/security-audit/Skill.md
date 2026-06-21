---
name: wp-plugin-security-audit
description: Perform a line-by-line security audit of a WordPress/WooCommerce plugin's source code and write findings to a ai-security-audit.md report with risk ratings. Use this skill whenever the user asks to audit, security-check, scan, or review plugin code for vulnerabilities, security issues, or weaknesses — especially when they mention the `includes` directory, a WooCommerce extension, SQLi/XSS/CSRF, nonce/capability/sanitization checks, or want a written security report. Trigger even if the user just says "check my plugin for security issues" without naming a specific vulnerability.
---

# WordPress / WooCommerce Plugin Security Audit

Audit every PHP (and where relevant JS) file under the target directory (default: `includes/`) **line by line**, identify security issues, and write a structured report to `ai-security-audit.md`.

This is a manual, code-reading audit — not a linter run. Read the actual code paths, follow user input from source (request superglobals, REST/AJAX params, DB rows, file contents) to sink (DB query, HTML output, filesystem, shell, redirect), and judge whether each sink is properly defended.

## Workflow

1. **Locate the target.** Default to `includes/`. If the user named another directory, use it. Recursively enumerate files (`*.php` first; also `*.js` if the user asks for JS or the plugin ships front-end logic). Confirm the file list if it's large or ambiguous.
2. **Read each file in full.** Do not skim or sample. For large files, read in chunks but cover every line. Track line numbers — every finding must cite the file and line(s).
3. **For each file, walk through the checklist below.** Trace data flow rather than pattern-matching: an unescaped variable is only an XSS bug if attacker-influenced data can reach it; a raw `$wpdb->query()` is only SQLi if user input reaches the string. Distinguish real exploitable issues from theoretical ones, and say which is which.
4. **Rate each finding** using the risk rubric.
5. **Write findings to `security-audit.md`** in the exact format below, in the target directory (or alongside it). Group by file, ordered by descending severity within each file. Keep a summary table at the top.
6. **Be honest about coverage and uncertainty.** If a file is generated, vendored (`vendor/`), or minified, note that you reviewed it shallowly. If exploitability depends on context you can't see (how a function is called elsewhere), flag it as "needs verification" rather than asserting.

## What to look for

Trace every piece of externally-controlled data (`$_GET`, `$_POST`, `$_REQUEST`, `$_COOKIE`, `$_SERVER`, `$_FILES`, REST request params, AJAX params, `WP_Query` args built from input, `file_get_contents` of remote/user data, deserialized data) to where it is used.

### Input handling & SQL injection
- Direct `$wpdb->query()` / `get_results()` / `get_var()` / `get_row()` with interpolated variables instead of `$wpdb->prepare()`. **Note:** `prepare()` with an unquoted `%s`/`%d` is fine, but `prepare()` used only on *part* of the query while concatenating raw input elsewhere is still injectable. `%i` (WP 6.2+) is required for identifiers (table/column names) — backtick-wrapping user input is not safe.
- `esc_sql()` used as a substitute for `prepare()` (it isn't, on its own, for values in a clause).
- Dynamic `ORDER BY` / `LIMIT` / column names built from request data without an allowlist.
- Missing `sanitize_*` on input before use (`sanitize_text_field`, `sanitize_key`, `absint`, `sanitize_email`, `sanitize_textarea_field`, `wp_kses_post`, etc.). Sanitization is not escaping — confirm both happen at the right boundaries.

### Output escaping & XSS
- Echoing variables without `esc_html()` / `esc_attr()` / `esc_url()` / `esc_js()` / `esc_textarea()` / `wp_kses()` / `wp_kses_post()`.
- Unescaped data in HTML attributes, `<script>`, inline `style`, `href`/`src`, or `data-*` attributes.
- `printf`/`sprintf` building HTML where `%s` carries untrusted data unescaped.
- Translation functions outputting unescaped: prefer `esc_html__()` / `esc_attr_e()` over bare `_e()` / `__()` when the string is printed.
- Stored XSS: data saved unsanitized to the DB (post meta, options, custom tables) then later echoed unescaped.
- Reflected XSS via `$_GET`/`$_SERVER['REQUEST_URI']`/`PHP_SELF`/`add_query_arg()`/`remove_query_arg()` (these do **not** escape — wrap output in `esc_url()`).

### CSRF / nonce verification
- State-changing actions (save, delete, update, import, bulk action, settings write) with no nonce check: `wp_verify_nonce()`, `check_admin_referer()`, `check_ajax_referer()`.
- Nonce created but never verified, or verified with the wrong action string.
- Relying on nonces alone for authorization (nonces prove intent, not permission — capability checks are still required).

### AuthZ / capability checks & IDOR
- Admin/privileged actions missing `current_user_can()` with an appropriate capability (`manage_options`, `manage_woocommerce`, `edit_shop_orders`, `edit_others_posts`, etc.).
- Object access by ID from request without verifying ownership/permission (IDOR) — e.g. loading an order, subscription, user, or file by an ID the requester supplies, without checking they may access it.
- `is_admin()` mistaken for a capability check (it only means "admin-area request", not "user is admin").

### AJAX & REST endpoints
- `wp_ajax_nopriv_*` handlers exposing privileged or data-leaking actions to unauthenticated users.
- `register_rest_route()` with `permission_callback` set to `__return_true` (or omitted) on endpoints that read/write sensitive data or mutate state.
- REST args without `sanitize_callback` / `validate_callback`.
- WooCommerce Store API / custom endpoints exposing customer PII, order details, or pricing logic without auth.

### File operations
- Path traversal: user input reaching `include`/`require`/`fopen`/`file_get_contents`/`unlink`/`readfile` without normalizing and confining to an allowed base path. Watch for `../`, null bytes, absolute paths, and `php://`/`phar://`/`data://` wrappers.
- Local/Remote File Inclusion via dynamic `include $_GET[...]`.
- Insecure uploads: `move_uploaded_file` / `wp_handle_upload` without MIME/extension allowlisting, or trusting `$_FILES['...']['type']`. Use `wp_check_filetype_and_ext()`; never allow `.php`/`.phtml`/`.phar` etc.
- Arbitrary file download endpoints that accept a path/filename from the request.

### Dangerous functions / code execution
- `eval()`, `assert()` with dynamic input, `create_function()`.
- `exec`, `shell_exec`, `system`, `passthru`, `popen`, `proc_open`, backticks — especially with any request-derived argument; require `escapeshellarg()`/`escapeshellcmd()` and justify why a shell call exists at all.
- `call_user_func`/`call_user_func_array`/variable functions/variable variables where the callback name comes from input.
- `preg_replace` with the `/e` modifier (deprecated, RCE).
- Dynamic `extract()` on request data (variable injection).

### Deserialization & data parsing
- `unserialize()` / `maybe_unserialize()` on untrusted input (PHP object injection → potential RCE via gadget chains). Prefer `json_decode()`; if `unserialize` is unavoidable, pass `['allowed_classes' => false]`.
- Untrusted XML parsed without disabling external entities (XXE) — `libxml_disable_entity_loader` / safe `simplexml`/`DOMDocument` config.

### SSRF & outbound requests
- `wp_remote_get`/`wp_remote_post`/`curl_*`/`file_get_contents` to a URL derived from user input without host allowlisting — can hit internal services / cloud metadata endpoints.
- Webhook/callback URLs accepted from requests and fetched server-side.

### Redirects
- `wp_redirect()` (no host validation) with a user-supplied URL → open redirect. Prefer `wp_safe_redirect()` and/or validate against allowed hosts.

### Secrets, crypto & randomness
- Hardcoded API keys, passwords, tokens, salts in source.
- Weak randomness for security tokens/nonces: `rand()`/`mt_rand()`/`uniqid()`. Use `wp_generate_password()`, `random_bytes()`, `random_int()`, or `wp_create_nonce()` appropriately.
- Weak hashing for passwords/secrets (`md5`/`sha1`) instead of `wp_hash_password()` / `password_hash()`; secret comparisons not using `hash_equals()` (timing-safe).

### WooCommerce-specific
- Reading/writing order, customer, subscription, or coupon data without `current_user_can('manage_woocommerce')` / `edit_shop_orders` or ownership checks.
- Exposing PII (emails, addresses, phone, order line items) in AJAX/REST responses, logs, or front-end output.
- Trusting client-submitted prices, totals, quantities, or coupon values instead of recalculating server-side.
- Custom cart/checkout/Store API logic bypassing WooCommerce's own validation and nonces (`woocommerce-process-checkout-nonce`, Store API `Nonce`/cart token).
- Storing gateway credentials or webhook secrets in plaintext options without note/justification.

### Information disclosure & misc
- `var_dump`/`print_r`/`error_log` of sensitive data; `error_reporting`/`display_errors` toggled on.
- Debug/test/backdoor endpoints left in code.
- Verbose error messages leaking paths, queries, or stack traces to users.
- Direct file access not blocked (`if ( ! defined( 'ABSPATH' ) ) exit;` missing at top of class/include files).
- Type-juggling auth bypass (`==` vs `===`, loose comparison of tokens/hashes).

## Risk rubric

Assign one level per finding. Judge by **realistic impact × ease of exploitation** in a typical install.

- **Critical** — Unauthenticated RCE, SQL injection, arbitrary file upload/read/write, auth bypass, or object injection reachable by any visitor. Drop-everything severity.
- **High** — Exploitable by low-privileged or unauthenticated users with serious impact: stored XSS, CSRF on a damaging action, IDOR exposing other users' data, SSRF to internal services, privilege escalation. Or a Critical-class bug that requires some authentication.
- **Medium** — Real but constrained: reflected XSS needing user interaction, missing capability check behind an existing nonce, info disclosure of non-critical data, open redirect, weak randomness for non-critical tokens. Often requires authenticated/admin context or specific conditions.
- **Low** — Hardening gaps and best-practice violations with limited direct impact: missing `ABSPATH` guard, bare `_e()` instead of `esc_html_e()` on low-risk strings, loose comparison without a clear bypass, minor info leak.
- **Info** — Not a vulnerability; defensive note, code-quality observation, or something to confirm during a deeper review.

If you cannot confirm exploitability from the code alone, rate it at your best estimate and append **(needs verification)** with what you'd need to check.

## Report format

Write to `security-audit.md`. Use this exact structure:

```markdown
# Security Audit — <plugin name>

- **Scope:** <directory audited, e.g. `includes/`>
- **Files reviewed:** <count> (<count> PHP, <count> JS)
- **Date:** <YYYY-MM-DD>
- **Method:** Manual line-by-line source review.

## Summary

| Severity | Count |
|----------|-------|
| Critical | 0 |
| High     | 0 |
| Medium   | 0 |
| Low      | 0 |
| Info     | 0 |

<One-paragraph overall assessment: posture, recurring weak spots, top priorities.>

## Findings

### `includes/path/to/file.php`

#### [HIGH] SQL injection in product lookup
- **Lines:** 142–148
- **Category:** SQL Injection
- **Description:** `$_GET['pid']` is concatenated directly into a `$wpdb->get_row()` query with no `prepare()`, allowing arbitrary SQL via the `pid` parameter.
- **Evidence:**
  ```php
  $pid = $_GET['pid'];
  $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}sp_products WHERE id = $pid" );
  ```
- **Impact:** Unauthenticated DB read/write depending on query context; potential full data disclosure.
- **Remediation:** Use a prepared statement and cast the ID:
  ```php
  $pid = absint( $_GET['pid'] ?? 0 );
  $row = $wpdb->get_row( $wpdb->prepare(
      "SELECT * FROM {$wpdb->prefix}sp_products WHERE id = %d", $pid
  ) );
  ```

#### [LOW] Missing direct-access guard
- **Lines:** 1
- **Category:** Hardening
- **Description:** File lacks an `ABSPATH` check, so it can be requested directly.
- **Remediation:** Add `if ( ! defined( 'ABSPATH' ) ) { exit; }` at the top.

### `includes/another-file.php`

<...>

## Notes & coverage gaps

- <Files reviewed only shallowly (vendored/generated/minified), assumptions made, and anything recommended for a deeper manual or dynamic test.>
```

Rules for the report:
- Every finding cites **file + line number(s)**.
- Severity tag in the heading uses uppercase brackets: `[CRITICAL]` `[HIGH]` `[MEDIUM]` `[LOW]` `[INFO]`.
- Include a short code snippet as evidence and a concrete, copy-pasteable fix using the correct WordPress/WooCommerce API.
- Keep the summary table counts accurate.
- If a file is clean, you may omit it from Findings but note in coverage that it was reviewed and no issues were found.
- Don't invent issues to pad the report. A short, accurate report beats a long speculative one.

## Example finding (escaping)

**Input (code under review):**
```php
echo '<div class="notice">' . $_GET['msg'] . '</div>';
```

**Output (report entry):**
> #### [MEDIUM] Reflected XSS in admin notice
> - **Lines:** 88
> - **Category:** Cross-Site Scripting (Reflected)
> - **Description:** `$_GET['msg']` is echoed into HTML without escaping, allowing script injection via the `msg` query parameter.
> - **Remediation:** `echo '<div class="notice">' . esc_html( wp_unslash( $_GET['msg'] ?? '' ) ) . '</div>';`
