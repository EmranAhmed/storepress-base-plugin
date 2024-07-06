/**
 * External dependencies
 */

const { defineConfig, devices } = require('@playwright/test');
/**
 * WordPress dependencies
 */
const baseConfig = require( '@wordpress/scripts/config/playwright.config' );

/**
 * @see https://playwright.dev/docs/test-configuration
 */

const config = defineConfig( {
	...baseConfig,
	testDir: './tests/e2e',
	outputDir: `./tests/e2e/test-results`,
	/* Reporter to use. See https://playwright.dev/docs/test-reporters */
	// reporter:  'html', // html, list
	reporter: [['html', { outputFolder: './tests/e2e/test-report' }]],

	workers: 1,
	use: {
		...baseConfig.use,
		// baseURL: process.env.WP_BASE_URL || 'http://localhost:8889',
		headless: true,
		// @see https://playwright.dev/docs/test-use-options#more-browser-and-context-options
		/*launchOptions: {
			slowMo: 3000, // in milliseconds
		},*/
	},
	webServer: {
		command: 'npx @wp-now/wp-now start --port=8889 --skip-browser',
		port: 8889,
		timeout: 120_000, // 120 seconds.
		reuseExistingServer: true,
	},
	projects: [
		{
			name: 'chromium',
			use: { ...devices[ 'Desktop Chrome' ] },
		},
	],
} );

export default config;
