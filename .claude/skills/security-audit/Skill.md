---
name: security-audit
version: 1.1.0
last-updated: 2026-11-11
description: Perform a line-by-line security audit of a WordPress/WooCommerce plugin's source code and write findings to a `security-audit-report.md` report with risk ratings. Use this skill whenever the user asks to audit, security-check, scan, or review plugin code for vulnerabilities, security issues, or weaknesses — especially when they mention the `includes` directory, a WooCommerce extension, SQLi/XSS/CSRF, nonce/capability/sanitization checks, or want a written security report. Trigger even if the user just says "check my plugin for security issues" without naming a specific vulnerability.
---

# WordPress / WooCommerce Plugin Security Audit

Audit every PHP (and where relevant JS) file under the target directory (default: `includes/`) **line by line**, identify security issues, and write a structured report to `security-audit-report.md`.

This is a manual, code-reading audit — not a linter run. Read the actual code paths, follow user input from source (request superglobals, REST/AJAX params, DB rows, file contents) to sink (DB query, HTML output, filesystem, shell, redirect), and judge whether each sink is properly defended.

## Workflow

1. **Locate the target.** Default to `includes/`. If the user named another directory, use it. Recursively enumerate files (`*.php` first; also `*.js` if the user asks for JS or the plugin ships front-end logic). Confirm the file list if it's large or ambiguous.
2. **Read each file in full.** Do not skim or sample. For large files, read in chunks but cover every line. Track line numbers — every finding must cite the file and line(s).
3. **For each file, walk through the checklist below.** Trace data flow rather than pattern-matching: an unescaped variable is only an XSS bug if attacker-influenced data can reach it; a raw `$wpdb->query()` is only SQLi if user input reaches the string. Distinguish real exploitable issues from theoretical ones, and say which is which.
4. **Find the entry point for each finding.** Work out how a request actually reaches the vulnerable line — the hook (`init`, `admin_post_*`, `wp_ajax_*`,`wc_ajax_*`, `rest_api_init`, shortcode, admin page slug), the URL or action name, the HTTP method, the parameters, and the minimum role needed. This is what the **Steps to reproduce** are built from.
5. **Rate each finding** using the risk rubric.
6. **Write findings to `security-audit-report.md`** in the exact format below, in the target directory (or alongside it). Group by file, ordered by descending severity within each file. Keep a summary table at the top.
7. **Be honest about coverage and uncertainty.** If a file is generated, vendored (`vendor/`), or minified, note that you reviewed it shallowly. If exploitability depends on context you can't see (how a function is called elsewhere), flag it as "needs verification" rather than asserting.

## What to look for

Trace every piece of externally-controlled data (`$_GET`, `$_POST`, `$_REQUEST`, `$_COOKIE`, `$_SERVER`, `$_FILES`, REST request params, AJAX params, `WP_Query`, `WC_Query` args built from input, `file_get_contents` of remote/user data, deserialized data) to where it is used.

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
- Direct file access not blocked (`if ( ! defined( 'ABSPATH' ) ) exit;` or `defined( 'ABSPATH' ) || die( 'Keep Silent' );` missing at top of class/include files).
- Type-juggling auth bypass (`==` vs `===`, loose comparison of tokens/hashes).

## Risk rubric

Assign one level per finding. Judge by **realistic impact × ease of exploitation** in a typical install.

- **Critical** — Unauthenticated RCE, SQL injection, arbitrary file upload/read/write, auth bypass, or object injection reachable by any visitor. Drop-everything severity.
- **High** — Exploitable by low-privileged or unauthenticated users with serious impact: stored XSS, CSRF on a damaging action, IDOR exposing other users' data, SSRF to internal services, privilege escalation. Or a Critical-class bug that requires some authentication.
- **Medium** — Real but constrained: reflected XSS needing user interaction, missing capability check behind an existing nonce, info disclosure of non-critical data, open redirect, weak randomness for non-critical tokens. Often requires authenticated/admin context or specific conditions.
- **Low** — Hardening gaps and best-practice violations with limited direct impact: missing `ABSPATH` guard, bare `_e()` instead of `esc_html_e()` on low-risk strings, loose comparison without a clear bypass, minor info leak.
- **Info** — Not a vulnerability; defensive note, code-quality observation, or something to confirm during a deeper review.

If you cannot confirm exploitability from the code alone, rate it at your best estimate and append **(needs verification)** with what you'd need to check.

## Writing "Steps to reproduce"

Every finding (except **Info**) gets a **Steps to reproduce** block that lets the plugin's developer confirm the bug on their own test install and later confirm the fix. Build it from the entry point you traced in workflow step 4. Good steps contain:

1. **Preconditions** — test environment (local or staging site the user owns, never production or a third-party site), plugin settings that must be enabled, and the **minimum role** needed (unauthenticated, Subscriber, Customer, Shop Manager, Administrator).
2. **The exact request** — URL, HTTP method, parameters and body, as a `curl` command or as browser steps. Use placeholders: `<site>` for the test site URL, `<nonce>` for a nonce copied from page source, `<cookie>` for a session cookie, `<plugin-slug>` for the plugin folder.
3. **A benign proof payload** — something that proves the bug without doing damage:
   - XSS: `alert(document.domain)`, never cookie theft.
   - SQLi: `SLEEP(5)` timing or `AND 1=1` / `AND 1=2` boolean differences, never `DROP`/`DELETE`/`UPDATE`.
   - File read/upload/command execution: a marker string such as `sp-audit-49` (from `7*7`) or a canary file you created, never real secrets or system files beyond confirming access.
   - Destructive actions (delete, refund, settings change): target test data created for the purpose.
4. **Expected vs. vulnerable result** — what a secure plugin returns (e.g. `403`, `-1`, escaped output) and what the vulnerable one returns. This doubles as the regression check after the fix.
5. **Verification status** — if the entry point depends on code you couldn't see, write the steps as the test to run and mark the finding **(needs verification)**.

Keep steps short and numbered. Do not write weaponized exploits, gadget chains, or automated attack scripts — the goal is confirmation, not exploitation.

### Reproduction recipes by category

Adapt these to the actual hook, parameter names, and role for each finding.

**SQL injection (time-based / boolean)**
1. As the role the endpoint allows (e.g. logged out), send a baseline request and note response time and body:
   `curl -s -o /dev/null -w "%{time_total}\n" "<site>/wp-admin/admin-ajax.php?action=sp_lookup&pid=1"`
2. Send the timing probe: `curl -s -o /dev/null -w "%{time_total}\n" "<site>/wp-admin/admin-ajax.php?action=sp_lookup&pid=1%20AND%20SLEEP(5)"`
3. Send boolean probes `pid=1%20AND%201=1` and `pid=1%20AND%201=2` and compare bodies.
4. **Vulnerable:** step 2 takes ~5 s longer than baseline, or step 3 returns different results. **Fixed:** all requests return as fast as baseline and identical to `pid=1`.

**Dynamic ORDER BY / column name**
1. Request the listing with `orderby=id` and then `orderby=(CASE WHEN 1=1 THEN id ELSE title END)`.
2. **Vulnerable:** the expression is accepted (different sort or SQL error). **Fixed:** unknown values fall back to the default column from the allowlist.

**Reflected XSS**
1. Log in as Administrator on the test site (or stay logged out for front-end pages).
2. Open `<site>/wp-admin/admin.php?page=<plugin-page>&msg=%3Cscript%3Ealert(document.domain)%3C/script%3E`
3. For attribute contexts use `msg=%22%3E%3Cimg%20src=x%20onerror=alert(document.domain)%3E`.
4. **Vulnerable:** an alert box appears, or View Source shows the raw `<script>` / `<img>` tag. **Fixed:** the payload renders as visible text (`&lt;script&gt;`).

**Stored XSS**
1. Log in as the lowest role that can write the field (e.g. Customer, Contributor, Shop Manager).
2. Save `<img src=x onerror=alert(document.domain)>` into the vulnerable field (profile field, product meta, order note, plugin setting).
3. Log in as Administrator in another browser and open the page that displays the field.
4. **Vulnerable:** the alert fires in the admin's session. **Fixed:** the payload is stripped on save or shown as escaped text.

**CSRF (missing nonce)**
1. Create a local HTML file `csrf-test.html`:
   ```html
   <form action="<site>/wp-admin/admin-post.php" method="POST">
     <input type="hidden" name="action" value="sp_save_settings">
     <input type="hidden" name="sp_api_mode" value="csrf-test-marker">
   </form>
   <script>document.forms[0].submit();</script>
   ```
2. Log in to the test site as Administrator, then open `csrf-test.html` in the same browser.
3. **Vulnerable:** the setting now reads `csrf-test-marker`. **Fixed:** WordPress shows "The link you followed has expired" / returns `403` and nothing changes.

**Missing capability check**
1. Create a Subscriber (or Customer) test user and log in as them.
2. If the action needs a nonce, copy it from the page source the low-privileged user can see (e.g. a localized `sp_vars.nonce`).
3. Send the privileged request with their cookie:
   `curl -s -b "<cookie>" -X POST "<site>/wp-admin/admin-ajax.php" -d "action=sp_delete_log&nonce=<nonce>&id=<test-id>"`
4. **Vulnerable:** the action succeeds. **Fixed:** response is `-1`, `403`, or an error and nothing changes.

**IDOR (other users' objects)**
1. Create two test customers, A and B, and place one test order as each. Note B's order ID.
2. Log in as A and request B's object: `curl -s -b "<cookie-A>" "<site>/wp-admin/admin-ajax.php?action=sp_get_order&order_id=<B-order-id>"`
3. **Vulnerable:** B's name, address, or line items are returned. **Fixed:** access is denied or nothing is returned.

**Unauthenticated AJAX (`wp_ajax_nopriv_*`)**
1. With no cookies: `curl -s -X POST "<site>/wp-admin/admin-ajax.php" -d "action=sp_export_orders"`
2. **Vulnerable:** data is returned or the action runs. **Fixed:** `0`/`-1`, `403`, or a generic error.

**REST route with open `permission_callback`**
1. List the route: `curl -s "<site>/wp-json/" | grep "<namespace>"` (or open `<site>/wp-json/<namespace>`).
2. Call it with no authentication: `curl -s -i "<site>/wp-json/sp/v1/orders"` (and `-X POST` with a test body for write routes).
3. **Vulnerable:** `200` with sensitive data, or the write succeeds. **Fixed:** `401 rest_forbidden`.

**Path traversal / arbitrary file read**
1. On the test server create a canary file outside the allowed directory, e.g. `wp-content/sp-audit-canary.txt` containing `sp-audit-canary`.
2. Request it through the vulnerable parameter: `curl -s "<site>/?sp_download=1&file=../../sp-audit-canary.txt"` (adjust the number of `../` to the base path in the code).
3. **Vulnerable:** the response contains `sp-audit-canary`. **Fixed:** an error, and the file is not served.

**Insecure file upload**
1. Create `sp-audit.php` containing `<?php echo 'sp-audit-' . (7*7);`.
2. Upload it through the plugin's upload form or endpoint as the lowest role allowed (also try `sp-audit.php.jpg` and setting `Content-Type: image/jpeg`).
3. Open the uploaded file's URL.
4. **Vulnerable:** the page prints `sp-audit-49` (code executed). **Fixed:** the upload is rejected, or the file is served as plain text/download only. Delete the test file afterwards.

**Command injection**
1. Send the parameter with a harmless marker: `param=test%3Becho%20sp-audit-%24((7*7))` or a timing probe `param=test%3Bsleep%205`.
2. **Vulnerable:** output contains `sp-audit-49`, or the response is delayed ~5 s. **Fixed:** the value is treated as a literal argument.

**PHP object injection (`unserialize`)**
1. Send a serialized harmless built-in object in the parameter/cookie, e.g. `O:8:"stdClass":1:{s:4:"test";s:3:"abc";}` (URL-encoded).
2. With `WP_DEBUG_LOG` on, check `wp-content/debug.log` for notices showing the object was instantiated (e.g. "Object of class stdClass could not be converted to string").
3. **Vulnerable:** the object is created. **Fixed:** input is rejected or decoded with `json_decode()` / `allowed_classes => false`. Usually mark **(needs verification)** — whether this becomes RCE depends on available gadget classes; do not build a gadget chain.

**SSRF**
1. On the test server start PHP's built-in web server as a listener: `php -S 127.0.0.1:8081` (run it in an empty folder so it serves nothing).
2. Submit the URL parameter as `http://127.0.0.1:8081/sp-audit-ssrf`.
3. **Vulnerable:** the terminal running `php -S` logs a request for `/sp-audit-ssrf` (it shows as a 404, which is fine — the hit is the proof).

**Open redirect**
1. `curl -s -i "<site>/?sp_redirect=https://example.org/"`
2. **Vulnerable:** `Location: https://example.org/`. **Fixed:** redirects to the site's own URL or admin fallback.

**WooCommerce client-side price / total tampering**
1. On the test store enable a test payment method (Cash on delivery or a gateway in test mode).
2. Open DevTools → Network, add a product to the cart, and find the plugin's add-to-cart / checkout request.
3. Right-click → "Copy as cURL", change the `price` / `total` / `discount` field (e.g. to `0.01`), and resend.
4. **Vulnerable:** the cart or created order uses the tampered value. **Fixed:** the server recalculates from the product price.

**Information disclosure / verbose errors**
1. Send malformed input (e.g. `pid[]=1`, `pid='`) to the endpoint.
2. **Vulnerable:** the response contains a server path, SQL query, stack trace, or PII. **Fixed:** a generic error message.

**Weak randomness / timing-unsafe comparison**
These are usually confirmed from code, not by request. Steps: point to where the token is generated (`mt_rand()`, `uniqid()`) or compared (`==`), explain what predicting/forging it would allow, and mark **(needs verification)** if a live test would be needed.

**Missing `ABSPATH` guard**
1. `curl -s -i "<site>/wp-content/plugins/<plugin-slug>/includes/path/to/file.php"`
2. **Vulnerable:** the response shows a PHP error with the full server path (when `display_errors` is on) or the file runs top-level code. **Fixed:** empty response from the `exit`.

## Report format

Write to `security-audit-report.md`. Use this exact structure:

````markdown
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

### File: `includes/path/to/file.php`

#### [HIGH] SQL injection in product lookup
- **File:** `includes/path/to/file.php`
- **Lines:** 142–148
- **Category:** SQL Injection
- **Description:** `$_GET['pid']` is concatenated directly into a `$wpdb->get_row()` query with no `prepare()`, allowing arbitrary SQL via the `pid` parameter.
- **Evidence:**
  ```php
  $pid = $_GET['pid'];
  $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}sp_products WHERE id = $pid" );
  ```
- **Impact:** Unauthenticated DB read/write depending on query context; potential full data disclosure.
- **Steps to reproduce:**
  1. On a local/staging copy with the plugin active, stay logged out. The code runs on the `wp_ajax_nopriv_sp_lookup` hook.
  2. Baseline: `curl -s -o /dev/null -w "%{time_total}\n" "<site>/wp-admin/admin-ajax.php?action=sp_lookup&pid=1"` — note the time (e.g. 0.3 s).
  3. Timing probe: `curl -s -o /dev/null -w "%{time_total}\n" "<site>/wp-admin/admin-ajax.php?action=sp_lookup&pid=1%20AND%20SLEEP(5)"`
  4. **Vulnerable:** step 3 takes ~5 s longer than step 2. **Fixed:** step 3 returns as fast as baseline and the same result as `pid=1`.
- **Suggested fix:** Use a prepared statement and cast the ID:
  ```php
  $pid = absint( $_GET['pid'] ?? 0 );
  $row = $wpdb->get_row( $wpdb->prepare(
      "SELECT * FROM {$wpdb->prefix}sp_products WHERE id = %d", $pid
  ) );
  ```

#### [LOW] Missing direct-access guard
- **File:** `includes/path/to/file.php`
- **Lines:** 1
- **Category:** Hardening
- **Description:** File lacks an `ABSPATH` check, so it can be requested directly.
- **Steps to reproduce:**
  1. `curl -s -i "<site>/wp-content/plugins/<plugin-slug>/includes/path/to/file.php"`
  2. **Vulnerable:** the response contains a PHP fatal error exposing the server path (e.g. `Uncaught Error: Class "WC_Settings_Page" not found in /var/www/html/wp-content/plugins/...`). **Fixed:** an empty `200` response.
- **Suggested fix:** Add `if ( ! defined( 'ABSPATH' ) ) { exit; }` at the top.

### File: `includes/another-file.php`

<...>

## Notes & coverage gaps

- <Files reviewed only shallowly (vendored/generated/minified), assumptions made, and anything recommended for a deeper manual or dynamic test.>
````

Rules for the report:
- Every finding cites **file + line number(s)**.
- Severity tag in the heading uses uppercase brackets: `[CRITICAL]` `[HIGH]` `[MEDIUM]` `[LOW]` `[INFO]`.
- Include a short code snippet as evidence and a concrete, copy-pasteable fix using the correct WordPress/WooCommerce API.
- Every non-Info finding has numbered **Steps to reproduce** with preconditions/role, the exact request, a benign payload, and the vulnerable vs. fixed result (see "Writing Steps to reproduce").
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
> - **File:** `includes/path/to/file.php`
> - **Lines:** 88
> - **Category:** Cross-Site Scripting (Reflected)
> - **Description:** `$_GET['msg']` is echoed into HTML without escaping, allowing script injection via the `msg` query parameter.
> - **Steps to reproduce:**
>   1. Log in to a test site as Administrator (the notice renders on the plugin's settings page, `page=sp-settings`).
>   2. Visit `<site>/wp-admin/admin.php?page=sp-settings&msg=%3Cscript%3Ealert(document.domain)%3C/script%3E`
>   3. **Vulnerable:** an alert showing the site's domain pops up, and View Source shows the raw `<script>` tag inside `<div class="notice">`. **Fixed:** the text `<script>alert(document.domain)</script>` is shown literally.
>   4. Note: an attacker would need to trick a logged-in admin into opening this link, hence Medium.
> - **Suggested fix:** `echo '<div class="notice">' . esc_html( wp_unslash( $_GET['msg'] ?? '' ) ) . '</div>';`
