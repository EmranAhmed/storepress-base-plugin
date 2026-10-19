/**
 * External dependencies
 */
const { defineConfig, devices } = require( '@playwright/test' );

/**
 * WordPress dependencies
 */
const baseConfig = require( '@wordpress/scripts/config/playwright.config' );

const slowMo = Number( process.env.SLOWMO || 500 );

module.exports = defineConfig( {
	...baseConfig,
	testDir: './tests/e2e',
	outputDir: './tests/e2e/test-results',
	reporter: [ [ 'html', { outputFolder: './tests/e2e/test-report', open: 'never' } ] ],
	workers: process.env.CI ? 2 : 1,
	use: {
		...baseConfig.use,
		baseURL: process.env.WP_BASE_URL || 'http://localhost:8889',
		trace: 'retain-on-failure', // on | retain-on-failure
		video: 'on',  // on | retain-on-failure
		launchOptions: { slowMo },
	},
	webServer: {
		command: 'npm run env:start',
		port: 8889,
		timeout: 120_000,
		reuseExistingServer: true,
	},
	projects: [
		{
			name: 'chromium',
			use: { ...devices[ 'Desktop Chrome' ] },
		},
	],
} );
