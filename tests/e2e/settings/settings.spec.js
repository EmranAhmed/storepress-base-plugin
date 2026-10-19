/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'StorePress Settings Page', () => {
	test( 'Loads and Shows the title', async ( { admin, page } ) => {
		await admin.visitAdminPage( 'admin.php', 'page=storepress-base-plugin' );

		await expect(
			page.getByRole( 'heading', { level: 1 } )
		).toBeVisible();

		await expect(page.getByRole('heading', { name: 'StorePress Base Plugin. -' })).toBeVisible();

	} );

	test( 'show hidden option', async ( { admin, page } ) => {
		await admin.visitAdminPage( 'admin.php', 'page=storepress-base-plugin' );

		await page.getByRole('textbox', { name: 'Input text' }).fill('x');

		await expect(page.getByRole('spinbutton', { name: 'Input text unit' })).toBeVisible();
	} );
} );
