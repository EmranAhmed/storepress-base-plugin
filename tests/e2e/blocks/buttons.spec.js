/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Buttons', () => {
	test.beforeEach( async ( { admin } ) => {
		await admin.createNewPost();
	} );

	test( 'has focus on button content', async ( { editor, page } ) => {
		await editor.insertBlock( { name: 'core/buttons' } );
		await expect(
			editor.canvas.locator( 'role=textbox[name="Button text"i]' )
		).toBeFocused();
		await page.keyboard.type( 'Content' );

		// Check the content.
		const content = await editor.getEditedPostContent();
		expect( content ).toBe(
			`<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Content</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->`
		);
	} );

	test( 'has focus on button content (slash inserter)', async ( {
		editor,
		page,
	} ) => {
		await editor.canvas
		.locator( 'role=button[name="Add default block"i]' )
		.click();
		await page.keyboard.type( '/buttons' );
		await page.keyboard.press( 'Enter' );
		await page.keyboard.type( 'Content' );

		// Check the content.
		const content = await editor.getEditedPostContent();
		expect( content ).toBe(
			`<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Content</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->`
		);
	} );

	test( 'dismisses link editor when escape is pressed', async ( {
		editor,
		page,
		pageUtils,
	} ) => {
		// Regression: https://github.com/WordPress/gutenberg/pull/19885
		await editor.insertBlock( { name: 'core/buttons' } );
		await expect(
			editor.canvas.locator( 'role=textbox[name="Button text"i]' )
		).toBeFocused();
		await pageUtils.pressKeys( 'primary+k' );
		await expect(
			page.locator( 'role=combobox[name="Link"i]' )
		).toBeFocused();
		await page.keyboard.press( 'Escape' );
		await expect(
			editor.canvas.locator( 'role=textbox[name="Button text"i]' )
		).toBeFocused();
		await page.keyboard.type( 'WordPress' );

		// Check the content.
		const content = await editor.getEditedPostContent();
		expect( content ).toBe(
			`<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">WordPress</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->`
		);
	} );

} );
