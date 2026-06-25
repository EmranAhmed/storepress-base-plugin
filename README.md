# StorePress Base Plugin

Download [latest release](https://github.com/EmranAhmed/storepress-base-plugin/releases/latest/download/storepress-base-plugin.zip) |
Test the plugin [in your browser](https://playground.wordpress.net/?mode=seamless&blueprint-url=https://raw.githubusercontent.com/EmranAhmed/storepress-base-plugin/main/.wp-playground/blueprint.json) using Playground.


## Summary table

| Question                                                           | Yes → Directory     | ServiceProvider behaviour |
|--------------------------------------------------------------------|---------------------|---|
| Extends an external package (functional)?                          | `Adapters/`         | — no own provider |
| Extends an external package (core infra like implement interface)? | `Integrations/`     | — no own provider |
| Has WordPress hooks that must fire on boot?                        | `Features/`         | `register()` **+** `boot()` |
| No hooks — resolved on demand?                                     | `Services/`         | `register()` only |
| Is an individual service provider?                                 | `ServiceProviders/` | — is the provider |
| Orchestrates all service providers?                                | `Core/`             | — boots `ServiceProviders/` |
| Shared utility across classes?                                     | `Traits/`           | — no provider |


**Why the difference matters:**

- `boot()` is what triggers hook registration — it resolves the class from the container, which runs its constructor and fires `add_action`/`add_filter`. A Feature *must* be booted eagerly so its hooks are live before WordPress needs them.
- A Service has no hooks. It does not need to be resolved at boot time — the container holds the binding and resolves it lazily the first time something calls `get_settings()` or `$container->get(Settings::class)`. Calling `boot()` on a hookless class is unnecessary work.



## First

- `git clone https://github.com/EmranAhmed/storepress-base-plugin.git`
- `npm i`
- `npm run packages-update` - Update WordPress packages
- `composer update && composer dump-autoload` - Update WordPress packages

## Where to change

- Create test for wp only `bash bin/install-wp-tests.sh storepress_base_plugin_test root 'PASSWORD' localhost latest`
- Create test for with wc `bash bin/install-wp-tests-with-wc.sh storepress_base_plugin_test root 'PASSWORD' localhost latest`
- Open all files from `includes` directory and change namespace from `namespace StorePress\Base;` to `namespace Your_Company\Your_Plugin;`.
- Open `storepress-base-plugin.php` and change plugin header, text-domain, namespace, defined Constant `STOREPRESS_BASE_PLUGIN_FILE`, function name.
- Open `includes/Plugin.php` and change `STOREPRESS_BASE_PLUGIN_FILE` on `function get_plugin_file()` function.
- Open `storepress-base-plugin.php` to your plugin file name.
- Rename `storepress-base-plugin.php` to your plugin file name.
- Open `eslint.config.js` and change `allowedTextDomain: ['storepress-base-plugin'],`
- Open `composer.json` and change `name`, `description`, `autoload`
- Open `package.json` and change `name` as plugin file name, `description`, `repository`.
- Open `phpcs.xml` and change `<rule ref="WordPress.NamingConventions.PrefixAllGlobals">`, `<property name="text_domain" value="storepress-base-plugin"/>`
- Open `package.json` and change `files` for package files.
- [Test example](https://core.trac.wordpress.org/browser/trunk/tests/phpunit)
- [Gutenberg test example](https://github.com/WordPress/gutenberg/tree/trunk/phpunit)

## Develop

- `npm start`

### Create New Block(s)

- `npm run create-dynamic-block [block name]` - To add dynamic block
- `npm run create-static-block [block name]` - To add static block

## Lint

- `npm run lint:js` - Lint Javascript
- `npm run lint:js:report` - Lint Javascript and will generate `lint-report.html`. From terminal `open lint-report.html`
- `npm run lint:css` - Lint CSS
- `npm run lint:css:report` - Lint CSS and will generate `scss-report.txt` file.
- `npm run lint:php` - PHP Lint.
- `npm run lint:php:report` - PHP Lint report generate on `phpcs-report.txt`.
- `npm run stan:php` - PHP Stan.
- `npm run stan:php:report` - PHP Stan report on `phpstan-report.txt` file.

## Fix

- `npm run lint:js:fix` - Fix Javascript Lint Issue.
- `npm run lint:css:fix` - Fix SCSS Lint Issue.
- `npm run lint:php:fix` - Fix PHP Lint Issue.

## Format

- `npm run format:js` - Format Javascript
- `npm run format:css` - Format SCSS
- `npm run format:php` - Format PHP
- `npm run format` - Format `./src`

## Test

- [How to add automated unit tests to your WordPress plugin](https://developer.wordpress.org/news/2025/12/how-to-add-automated-unit-tests-to-your-wordpress-plugin/)
- `npm run test:unit` - JS Unit test
- `npm run test:e2e` - E2E test
- `npm run test:php` - PHP Unit test

## Release

- `npm run plugin-zip` - make zip based on `package.json` `files` list.
- `npm run package` - make directory based on `package.json` `files` list.

## Release to GitHub.com

- `git tag $(node -p "require('./package.json').version") && git push origin "$_"` Publish Tag
- `git tag -d $(node -p "require('./package.json').version") && git push origin --delete "$_"` - Delete Tag

## Provide your own translations

- `npm run language` - Make POT File
- Then follow this link to test
- [Check translation guide](https://developer.wordpress.org/block-editor/how-to-guides/internationalization/#provide-your-own-translations)
