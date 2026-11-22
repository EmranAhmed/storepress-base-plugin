/**
 * Vitest configuration, run through `wp-scripts test-unit-js`.
 *
 * @see node_modules/@wordpress/scripts/docs/vitest-migration.md
 */

/**
 * External dependencies
 */
import {createRequire} from 'node:module';
import {defineConfig} from 'vitest/config';

const require = createRequire(import.meta.url);

/**
 * Internal dependencies
 */
const {getWebPackAlias} = require('./tools/webpack-helpers');

export default defineConfig({
	resolve: {
		alias: {
			// Same aliases as webpack and jsconfig.json.
			...getWebPackAlias(),
		},
	},
	test: {
		environment: 'jsdom',
		globals: true, // Lets Testing Library register cleanup automatically.
		restoreMocks: true,
		reporters: ['tree'], // List every test grouped under its file and describe block.
		setupFiles: ['@testing-library/jest-dom/vitest'],
		include: ['tests/js/**/*.test.{js,jsx}']
	},
});
