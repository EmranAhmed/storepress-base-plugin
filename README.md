# StorePress Base Plugin

## First

- `git clone https://github.com/EmranAhmed/storepress-base-plugin.git`
- `npm install`
- `npm run packages-update` - Update WordPress packages

## Where to change

- Open all files from `includes` directory and change namespace from `namespace StorePress\Base;` to `namespace Your_Company\Your_Plugin;`.
- Open `storepress-base-plugin.php` and change plugin header, text-domain, namespace, defined Constant `STOREPRESS_BASE_PLUGIN_FILE`, function name.
- Open `includes/Plugin.php` and change `STOREPRESS_BASE_PLUGIN_FILE` on `function get_plugin_file()` function.
- Open `storepress-base-plugin.php` to your plugin file name.
- Rename `storepress-base-plugin.php` to your plugin file name.
- Open `.eslintrc.js` and change `allowedTextDomain: ['storepress-base-plugin'],`
- Open `composer.json` and change `name`, `description`, `autoload`
- Open `package.json` and change `name` as plugin file name, `description`, `repository`, `languages/storepress-base-plugin.pot`
- Open `phpcs.xml` and change `<rule ref="WordPress.NamingConventions.PrefixAllGlobals">`, `<property name="text_domain" value="storepress-base-plugin"/>`
- Open `bin/package.js` and change `files = glob(...` for package files.

## Develop:

- `npm start`

### Create New Block(s)

- `npm run create-dynamic-block [block name]` - To add dynamic block
- `npm run create-static-block [block name]` - To add static block

## Lint

- `npm run lint:js` - Lint Javascript
- `npm run lint:css` - Lint CSS
- `npm run lint:php` - PHP lint and will generate `phpcs-report.txt` file.

## Format

- `npm run format:js` - Format Javascript
- `npm run format:css` - Format SCSS
- `npm run format:php` - Format PHP
- `npm run format` - Format PHP, SCSS, JS

## Release:

- `npm run package` - make package directory based on `./bin/package.js` list.
- `npm run plugin-zip` - make zip based on `./bin/package.js` list.

## Provide your own translations

- `npm run language` - Make POT File
- Then follow this link to test
- https://developer.wordpress.org/block-editor/how-to-guides/internationalization/#provide-your-own-translations
