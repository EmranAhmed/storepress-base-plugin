---
name: update-structure
version: 1.0.0
last-updated: 2026-11-11
description: Restructure a StorePress plugin's Files layout. Use when asked to apply the plugin re structure or namespace update to a plugin.
---

# Plugin structure update

Apply the fixed set of changes below to a WordPress plugin, then verify them. Change exactly what is listed and nothing else.

## Rules

- **Scope.** Edit only namespace declarations, `use` statements, fully qualified class names (including in docblocks), the `Plugin.php` methods and class DocBlock given below, and the `@phpstan-consistent-constructor` tag in step 7. Do not reformat, rename, refactor, or "improve" anything else.
- **Order.** Do the steps in order. Step 2 must finish before step 3, otherwise the two `Integrations` names collide.
- **Moves.** Use `git mv` in a git repository so history is kept; otherwise use `mv`.
- **Stop instead of guessing.** If an expected file or directory is missing, or a target already exists, stop and report it.

## Steps

### 1. Discover first

Do not assume paths or the vendor namespace. Find them:

- `<includes>`: the directory containing `Plugin.php` and the `Integrations`, `Adapters`, `Core` and `ServiceProviders` directories (often `includes/`).
- `<Root>`: the root namespace, read from the `namespace` line in `Plugin.php` (confirm against `autoload.psr-4` in `composer.json`).

### 2. Integrations -> Containers

- Rename `<includes>/Integrations/` to `<includes>/Containers/`. It must contain `Container.php`.
- In `Container.php`, change the namespace from `<Root>\Integrations` to `<Root>\Containers`.
- Update every reference to `<Root>\Integrations\...` that points at these files to `<Root>\Containers\...`.

### 3. Adapters -> Integrations

- Rename `<includes>/Adapters/` to `<includes>/Integrations/`.
- In every file inside it, change the namespace from `<Root>\Adapters` to `<Root>\Integrations`.
- Update every reference to `<Root>\Adapters\...` to `<Root>\Integrations\...`.

### 4. Core/Bootstrap.php -> ServiceProviders/ServiceProviders.php

- Move `<includes>/Core/Bootstrap.php` to `<includes>/ServiceProviders/ServiceProviders.php`.
- Change its namespace from `<Root>\Core` to `<Root>\ServiceProviders`.
- Rename the class from `Bootstrap` to `ServiceProviders` so it matches the file name.
- Remove `<includes>/Core/` only if it is now empty.

### 5. ServiceProviders files

- In every file under `<includes>/ServiceProviders/`, update `use` statements and fully qualified names to the new namespaces from steps 2, 3 and 4.

### 6. Plugin.php

**6a. Namespaces and `service_providers()`**

- Update its `use` statements and references to the new namespaces.
- Change `service_providers()` so its return type and `@return` tag are `ServiceProviders`, and its body uses `ServiceProviders` wherever it used `Bootstrap`. Keep the rest of the body's logic as it is.

**6b. Replace `includes()`** with exactly:

```php
	/**
	 * Loads vendor autoloader and utility functions.
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function includes(): void {

		require_once untrailingslashit( plugin_dir_path( __DIR__ ) ) . '/includes/functions.php';

		$vendor_path = untrailingslashit( plugin_dir_path( __DIR__ ) ) . '/vendor';

		if ( file_exists( $vendor_path . '/autoload_packages.php' ) ) {
			require_once $vendor_path . '/autoload_packages.php';
		}
	}
```

**6c. Replace `instance()`** with exactly:

```php
	/**
	 * Returns the singleton instance.
	 *
	 * @return static
	 * @phpstan-return static
	 * @since 0.0.1
	 */
	public static function instance(): static {
		/**
		 * Static instances.
		 *
		 * @var array<string, static> $instances
		 */
		static $instances = array();

		if ( ! isset( $instances[ static::class ] ) ) {
			$instances[ static::class ] = new static();
		}

		return $instances[ static::class ];
	}
```

**6d. Class DocBlock** — write the doc comment for `class Plugin {}` with exactly:

```php
/**
 * Class Plugin.
 *
 * @name Plugin
 * @phpstan-consistent-constructor
 */
```

### 7. PHPStan consistent constructor

Add `@phpstan-consistent-constructor` in the class doc comment of every class that uses `SingletonTrait` from `StorePress\AdminUtils\Traits\SingletonTrait`.

### 8. Check for leftovers

Search the plugin, excluding `vendor/` and `node_modules/`, for:

- `<Root>\Adapters`
- `<Root>\Core\Bootstrap`
- `<Root>\Integrations\Container`

Inside the files covered by the steps above there must be no matches. If a match is in any other file (main plugin file, tests, config), change only that stale reference and list the file in the report.

### 9. Verify

1. If `vendor/` exists, run `composer dump-autoload` so the class map picks up the moved files.
2. Run `npm run lint:php`.

Both commands must exit with code 0.

- If a failure comes from a file this skill touched, fix it within the scope rules and rerun both commands.
- If a failure is unrelated to these changes, leave it and report it.
- Never silence a failure with ignore comments, baseline entries or config changes, and never run an auto-fixer across the whole project.

### 10. Report

Finish with a short summary:

- Files moved or renamed (old path → new path)
- Files edited
- Result of `npm run lint:php` and `npm run stan:php`
- Anything skipped, unexpected, or left for the user
