---
name: create-storepress-plugin
description: Write, extend, or refactor WordPress/WooCommerce plugin code exactly the way the StorePress base plugin does it — directory layout, namespaces, singleton + DI container + service-provider architecture, PHP/JS/SCSS code style, naming, docblocks, i18n, escaping, blocks, conventions, architecture, directory structure, naming, coding style, and documentation patterns and tests. Use whenever creating or editing any file in a StorePress plugin (or any plugin scaffolded from storepress-base-plugin), adding a feature, service, integration, settings tab, block, frontend script, or test, or when the user says "create like the base plugin", "StorePress Plugin Style", or "create like storepress plugin".
---

# StorePress Plugin Conventions

Reference implementation: https://github.com/EmranAhmed/storepress-base-plugin
Code skeletons for every file type: `references/templates.md` (read it before creating a new file).

## 0. Profile the project first (never hardcode)

Read these before writing anything, and reuse what they say:

- **Main plugin file** (`<namespace>-<slug>.php`): `Version`, `Text Domain`, `Requires PHP`, `Requires at least`.
  Never repeat these values elsewhere — derive them.
- **`composer.json` → `autoload.psr-4`**: the root namespace (base plugin: `StorePress\Base\` → `includes/`).
- **`phpcs.xml`**: text domain list and allowed global prefixes (`storepress`, `StorePress`, `STOREPRESS`).
- **`phpstan.neon`**: level (base plugin: `max`) and PHP range (7.4 → 8.5).
- **`tools/webpack-helpers.js` → `getWebPackAlias()`** and `jsconfig.json`: JS import aliases.
- **`CLAUDE.md`** if present.

In this file, `<Root>` = root namespace, `<slug>` = plugin slug / text domain, `<prefix>` = slug with
`_` instead of `-` (e.g. `storepress_base_plugin`), `<CONST>` = upper-case prefix.

## 1. Directory structure

```
<namespace>-<slug>.php     Bootstrap only: header, constant, accessor fn, plugins_loaded hook
uninstall.php              Cleanup guarded by WP_UNINSTALL_PLUGIN
includes/
  Plugin.php               Main singleton: includes() → hooks() → init(); lists service providers
  functions.php            Namespaced accessor functions: get_container(), get_settings(), get_<service>()
  Containers/              Container.php — extends AdminUtils ServiceContainer + SingletonTrait
  ServiceProviders/        One <Name>ServiceProvider per service + ServiceProviders.php loader
  Features/                User-facing features that hook into WP (e.g. Blocks.php)
  Services/                Reusable logic/state (Settings.php, BlockSupport.php)
  Integrations/            Subclasses of StorePress AdminUtils abstracts (AdminPage, Updater,
                           AdditionalAdminMenu, DeactivationFeedback, ProPluginInCompatibility)
  Traits/                  PluginUtilityTrait.php (shared helpers mixed into most classes)
templates/                 PHP view partials loaded via load_template(), receive $template_data
src/
  <block-name>/            block.json, index.js, edit.js, save.js, render.php, view.js|viewModule.js,
                           style.scss, editor.scss
  plugins/                 Frontend "Plugin" constructors (Plugin.js)
  utils/                   Shared JS helpers (index.js, BlockSupport.js)
  editor-scripts.js        Editor-wide JS entry
  frontend-scripts.js      Frontend JS entry
  frontend.scss            Frontend styles entry
tests/
  php/Unit/<Mirror of includes>/<Class>Test.php
  js/<area>/<name>.test.js
  e2e/<area>/<name>.spec.js
tools/                     Node build/package scripts (webpack-helpers.js, package.js, …)
build/ vendor/ languages/  Generated — never edit
```

Decide where a new class goes by **role**, not by topic:

| It… | Directory |
|---|---|
| registers WP hooks to deliver a user-facing feature | `Features/` |
| holds reusable logic, data, or config (no own hooks, or settings) | `Services/` |
| extends a `StorePress\AdminUtils\Abstracts\Abstract*` class | `Integrations/` |
| binds something into the container | `ServiceProviders/` |
| is shared method set reused across classes | `Traits/` |

## 2. Architecture (the logical decisions)

1. **Bootstrap file** defines `<CONST>_FILE`, declares `function <prefix>(): Plugin` (returns
   `Plugin::instance()`, leaves a commented hook for a `_pro` override) and
   `function <prefix>_init(): void`, then `add_action( 'plugins_loaded', '<prefix>_init' )`.
   No other logic lives there.
2. **`Plugin`** has a `protected __construct()` calling `includes()`, `hooks()`, `init()` in that
   order. `includes()` loads `functions.php` and `vendor/autoload_packages.php` (Jetpack autoloader).
   `init()` boots providers. WooCommerce HPOS compatibility is declared on `before_woocommerce_init`.
3. **Every service is a singleton** (`use SingletonTrait;`, `protected __construct()`, accessed via
   `::instance()`), tagged `@phpstan-consistent-constructor`.
4. **Every service is wired through its own service provider**, registered in
   `Plugin::get_service_providers()` as `Foo::class => Foo::class`:
  - `register()` binds a closure into the container that returns `Service::instance()`.
  - `boot()` resolves it with `$this->get_container()->get( Service::class )` (that instantiates
    it and attaches its hooks). Leave `boot()` empty for lazy/factory services.
  - For per-call objects (not singletons), bind a **factory**: the closure returns
    `static function ( array $args ): Thing { return new Thing( $args ); }` and document the
    resolved type with an inline `@var \Closure(...): Thing` (see `get_block_support()`).
5. **Access services through the container**, never by `new` or by globals:
  - inside classes: `$this->get_container()->get( X::class )` or a `PluginUtilityTrait` helper
    (`get_settings()`, `get_block_support()`);
  - in templates/render files: namespaced functions from `functions.php` (`get_blocks()`,
    `get_settings()`). Add a new `get_<service>()` there when a template needs a service.
6. **Classes with hooks** follow: `protected __construct()` → `$this->hooks(); $this->init();` →
   then fire a `do_action( '<prefix-short>_<feature>_loaded', $this )` signal
   (e.g. `storepress_base_blocks_loaded`). `hooks()` only contains `add_action`/`add_filter`, each
   preceded by a one-line comment. `init()` exists even if empty.
7. **Hook callbacks are public methods prefixed `handle_`** (`handle_register_blocks`,
   `handle_frontend_scripts`, `handle_add_block_category`), registered with `array( $this, '…' )`.
8. **Admin UI comes from `storepress/admin-utils`**: subclass the matching abstract
   (`AbstractSettings`, `AbstractUpdater`, `AbstractAdminMenu`, `AbstractDeactivationFeedback`,
   `AbstractProPluginInCompatibility`, `AbstractServiceProvider`). Implement only the required
   methods; put every user-visible string in `localize_strings()`.
9. **Settings** live in `Services/Settings.php` (extends `Integrations\AdminPage`):
   `add_settings()` returns tabs (`'id' => 'Name'` or `'id' => array( 'name', 'icon', 'sidebar', 'hidden', 'external' )`),
   and each tab `<id>` has `add_<id>_settings_fields(): array` returning field arrays
   (`id`, `type`, `title`, `description`, `default`, `options`, `condition` via
   `$this->get_field_selector()` / `get_group_field_selector()`, `show_in_rest`, …).
   Read options with `$this->get_settings()->get_option( 'key', 'default' )`, then guard the type.
10. **Blocks** are auto-registered: `Blocks::handle_register_blocks()` globs `build/**/block.json`.
    Never register a block manually — add a folder under `src/`.
11. **Assets** are enqueued from `build/` using `$this->build_url()`, `$this->build_path()`,
    `$this->get_script_asset( '<file>.asset.php' )`, with `array( 'strategy' => 'defer' )`.
    Script/style handles: `<slug>-<purpose>` (`storepress-base-plugin-editor-scripts`).
    Editor scripts call `wp_set_script_translations( handle, '<slug>', $this->languages_path() )`.
12. **Pro/extended plugin**: path comes from `get_pro_plugin_file()` (constant override
    `<CONST>_PRO_FILE`), compatibility from `ProPluginInCompatibility::compatible_version()`.

## 3. PHP code style

Base: WordPress-Core + WordPress-Extra + WordPress-Docs + WordPress-VIP-Go + WooCommerce-Core +
PHPCompatibilityWP (7.4+) + PluginCheck. PHPStan at the project's level (max).

- **File preamble, in this exact order:** `<?php`, file docblock, `declare( strict_types=1 );`,
  `namespace …;`, `defined( 'ABSPATH' ) || die( 'Keep Silent' );`, `use` statements, class docblock,
  class. **Everything above the class/function body is indented one tab**; the `class`/`trait`/
  top-level `function`/`if` line starts at column 0 (match existing files exactly).
  `functions.php` and `uninstall.php` are the exceptions: no extra indent.
- **File docblock:** one-line title ending with a period, then `@package StorePress/<Sub>`,
  `@since`, `@version` aligned in columns.
- **Class docblock:** one-line purpose sentence, `@name ClassName`, `@phpstan-consistent-constructor`
  for anything with `instance()`/`new static`. Optional `@since`, `@see`, `@example`.
- **`class Plugin {}` docblock:** always exactly this (indented one tab, like the rest of the
  preamble), replacing any existing one:

  ```php
  	/**
  	 * Class Plugin.
  	 *
  	 * @name Plugin
  	 * @phpstan-consistent-constructor
  	 */
  ```

- **PHPStan consistent constructor:** add `@phpstan-consistent-constructor` to the class docblock
  of every class that uses `SingletonTrait` from `StorePress\AdminUtils\Traits\SingletonTrait`.
- **Method docblock:** one summary line in present tense ("Returns …", "Registers …", "Binds …"),
  `@param` with precise type, `@return` (always, including `void`), `@since x.y.z`, then `@see` /
  `@example`. Use PHPStan generics/shapes: `array<string, mixed>`, `array<int, array<string, mixed>>`,
  `array{classes: array<string,bool>, css: string}`, `class-string<T>`.
- **Section dividers** group methods inside a class, in this order when present:
  ```php
  // =====================================================================
  // Service Lifecycle Methods
  // =====================================================================
  ```
  then `Hook Callbacks`, then `Utility Methods` (others like `Container Access Methods`, `Logging`
  are fine).
- **Always** full scalar/return type hints, `array()` long syntax (never `[]`), Yoda conditions
  (`false === $x`), strict `in_array( …, true )`, spaces inside parentheses and brackets
  (`$a[ $key ]`), tabs for indentation, aligned `=>` and `=` in consecutive lines.
- **Method names:** `snake_case`. Getters `get_*`, booleans `is_*`/`has_*`, callbacks `handle_*`.
  Classes `PascalCase`, files named after the class (`WordPress.Files.FileName` is disabled).
- **Imports:** `use` for classes; `use function <Root>\get_container;` for namespaced functions.
  Reference classes as `Foo::class`, never as strings.
- **Types from loose WP APIs:** add a runtime guard rather than a cast or ignore
  (`is_string( $v ) ? $v : ''`, `false === $files` return). When PHPStan cannot see a truth,
  document it with an inline `/** … @var Type $x */` that states *why*. Never add baselines,
  `ignoreErrors`, or lower the level.
- **Globals:** every global function/constant/hook/option is prefixed (`storepress`, `<prefix>_`).
  In templates and `render.php`, prefix local variables with `$storepress_`.
- **i18n:** every user-facing string uses the project text domain literal
  (`esc_html__( 'Text', '<slug>' )`). Placeholders go through `sprintf` with a
  `/* translators: %s: … */` comment immediately before. HTML in strings → `wp_kses_post( __( … ) )`.
- **Escaping:** escape late. Attributes `esc_attr()`, text `esc_html()`, block wrapper attributes
  `wp_kses_data()`, inner HTML `wp_kses( $content, $allowed_html )` with
  `get_blocks()->get_kses_allowed_html()`.
- Comments are full sentences ending with a period. Commented-out examples sit in `@example` or
  `/** … */` blocks.

## 4. Blocks (`src/<block-name>/`)

- Create with the npm scripts (`npm run create-dynamic-block` / `create-static-block` /
  `create-interactive-block`), which use namespace `storepress` and `--no-plugin`, then adapt.
- `block.json`: `$schema` trunk, `"apiVersion" : 3`, `"name" : "storepress/<block-name>"`,
  `"category" : "storepress"`, `"textdomain" : "<slug>"`, `editorScript` `file:./index.js`,
  `editorStyle` `file:./index.css`, `style` `file:./style-index.css`, `render` `file:./render.php`,
  `viewScript` `file:./view.js` (or `viewScriptModule` `file:./viewModule.js` +
  `"supports": { "interactivity": true }`). JSON is tab-indented with a space on both sides of the
  colon (`"key" : value`).
- Prefer dynamic blocks: `render.php` does the markup; `save.js` returns only inner blocks
  (`DynamicBlock` pattern) so markup changes never invalidate content.
- `render.php`: standard preamble + `use WP_Block;` + a docblock declaring
  `@var array{…} $attributes`, `@var WP_Block $block`, `@var string $content`. Build
  `$storepress_classes` (`array( 'class' => true )`) and `$storepress_styles`
  (CSS custom properties), pass them through `get_blocks()->get_css_classes()` /
  `get_inline_styles()` into `get_block_wrapper_attributes()`.
- Server-side block-support styles: `$this->get_block_support( $attributes )->get_styles( array( 'color', 'spacing' ) )`.
  Client side: `getBlockSupportStyles( attributes, [ … ] )` from `@block-support`.
- Interactivity API store namespace: `'storepress'`.

## 5. JavaScript style

- WordPress ESLint + WooCommerce ESLint + `@wordpress/prettier-config` (wp-prettier): tabs,
  single quotes, spaces inside parens/brackets/template braces (`fn( a )`, `[ a ]`, `${ x }`),
  semicolons, trailing commas.
- **Import groups**, each preceded by its docblock label, in this order:
  ```js
  /**
   * WordPress dependencies
   */
  /**
   * External dependencies
   */
  /**
   * Internal dependencies
   */
  ```
  Internal imports use aliases — `@utils`, `@plugins`, `@block-support`, `@/…` — never long
  relative paths. A new alias is added to `getWebPackAlias()` **and** `jsconfig.json`.
- `@wordpress/*` packages are externals (provided by WP), not `package.json` dependencies.
- Frontend scripts start with `'use strict';` and run inside `domReady( function () { … } )`.
- Utilities are named `export const fn = ( … ) => {}` with JSDoc (`@param {Type} name Desc.`,
  `@return {Type}`); React components are `export default function Edit( { attributes, setAttributes } )`.
- Frontend widgets follow the **Plugin pattern** (`src/plugins/Plugin.js`): a function
  `Plugin( element, options )` with `DEFAULTS`, an `ATTRIBUTE` name read through
  `getOptionsFromAttribute()`, private `addEvents` / `removeEvents`, an `expose()` returning the
  public API, and `init()` that merges settings and returns `expose()`. Instantiate with
  `createPluginInstance( selector, options, Plugin )` from `@storepress/utils`; drive init/destroy
  through `document` `CustomEvent`s (`<name>_init`, `<name>_destroy`, `<name>_init_with_options`).
- Use `window.console` (not bare `console`), `clsx` for class names, `__( '…', '<slug>' )` for text.

## 6. SCSS

- Start with `@charset "UTF-8";` and `@use "~@wordpress/base-styles/colors" as colors;`.
- `@wordpress/stylelint-config/scss` with the project's relaxed rules; tabs; block selectors
  `.wp-block-storepress-<block>` or `.storepress-<block>`.
- `style.scss` = front + editor, `editor.scss` = editor only.

## 7. Tests (not linted; keep their own style)

- **PHP**: `tests/php/Unit/<same path as includes>/<Class>Test.php`, `namespace Unit\<Dir>;`,
  `class <Class>Test extends WP_UnitTestCase`, Allman braces, `set_up()`/`tear_down()` calling
  parent, methods `test_<behaviour>()`, data providers named `<thing>_data_provider` with
  `@dataProvider`. Assert hooks with `has_action()` priorities, singletons via
  `assertSame( container->get( X::class ), X::instance() )`.
- **JS unit**: `tests/js/<area>/<name>.test.js`, `describe` / `it( 'should …' )`, Testing Library.
- **E2E**: `tests/e2e/<area>/<name>.spec.js` with `@wordpress/e2e-test-utils-playwright`.

## 8. Workflow and verification

1. Profile (step 0) and read the closest existing sibling file; copy its shape.
2. Place the file by role (step 1); wire it (provider → `get_service_providers()` → accessor in
   `functions.php` if templates need it).
3. After every change run the matching checks and fix what they report:
  - PHP: `npm run lint:php` (auto-fix `npm run lint:php:fix`), `npm run stan:php`
  - JS: `npm run lint:js` · CSS: `npm run lint:css` · package.json: `npm run lint:pkg-json`
  - Markdown: `npm run lint:md:docs` · Tests: `npm run test:php:env`, `npm run test:unit`
4. Do not touch `build/`, `vendor/`, `languages/`, lockfiles, or files outside the task without asking.
5. When reporting, list any convention you could not follow and why.

## Never

- Hardcode version, text domain, or PHP/WP minimums outside the plugin header and `phpcs.xml`.
- Instantiate services with `new` outside their provider/factory, or call `::instance()` from
  templates (use the accessor functions).
- Register blocks, hooks, or assets in `Plugin.php` — they belong to a Feature/Integration.
- Use short array syntax, loose comparisons, unprefixed globals, or untranslated UI strings.
- Silence PHPStan/PHPCS with ignores, baselines, or `phpcs:ignore` when a real fix exists.
