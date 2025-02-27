# StorePress Base Plugin


Download [latest release](https://github.com/EmranAhmed/storepress-base-plugin/releases/latest/download/storepress-base-plugin.zip) |
Test the plugin [in your browser](https://playground.wordpress.net/?mode=seamless&blueprint-url=https://raw.githubusercontent.com/EmranAhmed/storepress-base-plugin/main/.wp-playground/blueprint.json) using Playground.



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
- Open `.eslintrc.js` and change `allowedTextDomain: ['storepress-base-plugin'],`
- Open `composer.json` and change `name`, `description`, `autoload`
- Open `package.json` and change `name` as plugin file name, `description`, `repository`.
- Open `phpcs.xml` and change `<rule ref="WordPress.NamingConventions.PrefixAllGlobals">`, `<property name="text_domain" value="storepress-base-plugin"/>`
- Open `package.json` and change `files` for package files.
- Make package script executable `chmod +x ./tools/package.js`
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

- `npm run test:unit` - JS Unit test
- `npm run test:e2e` - E2E test
- `npm run test:php` - PHP Unit test

## Release

- `npm run plugin-zip` - make zip based on `package.json` `files` list.
- `npm run package` - make directory based on `package.json` `files` list.

## Release to GitHub.com

- `git tag 1.0.0 && git push origin "$_"` Publish Tag
- `git tag -d 1.0.0 && git push origin --delete "$_"` - Delete Tag

## Provide your own translations

- `npm run language` - Make POT File
- Then follow this link to test
- [Check translation guide](https://developer.wordpress.org/block-editor/how-to-guides/internationalization/#provide-your-own-translations)
