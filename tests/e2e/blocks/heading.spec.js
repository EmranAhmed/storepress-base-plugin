/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Heading', () => {
	test.beforeEach( async ( { admin } ) => {
		await admin.createNewPost();
	} );

	test( 'can be created by prefixing number sign and a space', async ( {
		editor,
		page,
	} ) => {
		await editor.canvas
		.locator( 'role=button[name="Add default block"i]' )
		.click();
		await page.keyboard.type( '### 3' );

		await expect.poll( editor.getBlocks ).toMatchObject( [
			{
				name: 'core/heading',
				attributes: { content: '3', level: 3 },
			},
		] );
	} );

	test( 'can be created by prefixing existing content with number signs and a space', async ( {
		editor,
		page,
	} ) => {
		await editor.canvas
		.locator( 'role=button[name="Add default block"i]' )
		.click();
		await page.keyboard.type( '4' );
		await page.keyboard.press( 'ArrowLeft' );
		await page.keyboard.type( '#### ' );

		await expect.poll( editor.getBlocks ).toMatchObject( [
			{
				name: 'core/heading',
				attributes: { content: '4', level: 4 },
			},
		] );
	} );

} );
