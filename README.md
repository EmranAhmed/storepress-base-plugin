# StorePress Base Plugin

## First
-  `git clone https://github.com/EmranAhmed/storepress-base-plugin.git`
- Install https://wp-cli.org/ if not available
- `npm install && composer install`
- `npm run packages-update` - Update wordpress packages

## Develop:

- `npm start`

### Create New Block(s)

- `npm run add [block name]` - To add dynamic block
- `npm run add-static [block name]` - To add static block

## Lint

- `npm run lint:js` - Lint Javascript
- `npm run lint:css` - Lint CSS
- `npm run lint:php` - PHP lint

- `composer run analyze [filename.php]` - statically analyzes PHP for bugs
- `composer run lint` - checks PHP for syntax errors
- `composer run standards:check` - checks PHP for standards errors according to WordPress coding standards
- `composer run standards:fix` - Attemps to automatically fix errors

## Release:

- `npm run package` - make package directory
- `npm run zip` - make zip
