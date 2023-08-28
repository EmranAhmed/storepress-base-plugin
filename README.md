# StorePress Base Plugin

## First
-  `git clone https://github.com/EmranAhmed/storepress-base-plugin.git`
- Install https://wp-cli.org/ if not available
- `npm install && composer install`
- `npm run packages-update`

## Develop:

- `npm start`

## Lint

- `npm run lint:js`
- `npm run lint:css`
- `npm run lint:php`

- `composer run analyze [filename.php]` statically analyzes PHP for bugs
- `composer run lint` checks PHP for syntax errors
- `composer run standards:check` checks PHP for standards errors according to WordPress coding standards
- `composer run standards:fix` attemps to automatically fix errors

## Release:

- `npm run package` - make package directory
- `npm run zip` - make zip
