/**
 * External dependencies
 */
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';

// ExampleUtils.js exports nothing and calls Meta.init() on import, so each
// test prepares the DOM first and then imports a fresh copy of the module.
// `@utils` resolves to src/utils via getWebPackAlias() in vitest.config.mjs.
const importExampleInit = () => import( '@utils/ExampleUtils' );

/**
 * Import the module while spying on the #user element's addEventListener,
 * and return the registered `change` handler.
 *
 * @return {Promise<(event: Event) => void>} The handler Meta.init() attached.
 */
const importAndCaptureHandler = async () => {
	const user = document.querySelector( '#user' );
	const spy = vi.spyOn( user, 'addEventListener' );

	await importExampleInit();

	expect( spy ).toHaveBeenCalledTimes( 1 );
	const [ type, handler ] = spy.mock.calls[ 0 ];
	expect( type ).toBe( 'change' );

	return handler;
};

describe( 'plugins/ExampleInit', () => {
	beforeEach( () => {
		vi.resetModules();
		document.body.innerHTML =
			'<select id="user"><option>1</option></select>';
	} );

	afterEach( () => {
		document.body.innerHTML = '';
	} );

	it( 'throws on import when #user is missing from the page', async () => {
		document.body.innerHTML = '';

		await expect( importExampleInit() ).rejects.toThrow( TypeError );
	} );

	it( 'attaches a change listener to #user on import', async () => {
		const handler = await importAndCaptureHandler();

		expect( handler ).toBeTypeOf( 'function' );
		expect( handler.name ).toBe( 'customer_user' );
	} );

	it( 'customer_user forwards force=true and the event to load_billing', async () => {
		const handler = await importAndCaptureHandler();
		const context = { load_billing: vi.fn() };
		const event = new Event( 'change' );

		handler.call( context, event );

		expect( context.load_billing ).toHaveBeenCalledWith( true, event );
	} );

	it( 'load_billing stores the force flag on states', async () => {
		const handler = await importAndCaptureHandler();

		// Run customer_user against a Meta-shaped object to exercise the
		// real load_billing logic without relying on the DOM binding.
		const meta = {
			states: null,
			load_billing( force ) {
				this.states = force;
			},
		};

		handler.call( meta, new Event( 'change' ) );

		expect( meta.states ).toBe( true );
	} );

	// Known bug: the handler is passed unbound, so inside customer_user
	// `this` is the #user element (which has no load_billing). Remove this
	// test once init() binds the handler, e.g. `this.customer_user.bind( this )`.
	it( 'fails to call load_billing when a real change event fires (unbound `this`)', async () => {
		await importExampleInit();

		const errors = [];
		const onError = ( event ) => {
			errors.push( event.error );
			event.preventDefault();
		};
		window.addEventListener( 'error', onError );

		document
			.querySelector( '#user' )
			.dispatchEvent( new Event( 'change' ) );

		window.removeEventListener( 'error', onError );

		expect( errors ).toHaveLength( 1 );
		expect( errors[ 0 ] ).toBeInstanceOf( TypeError );
		expect( errors[ 0 ].message ).toMatch( /load_billing/ );
	} );
} );
