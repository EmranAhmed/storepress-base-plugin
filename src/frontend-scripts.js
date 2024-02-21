/**
 * External dependencies
 */
import domReady from '@wordpress/dom-ready';
import { createPluginInstance } from '@storepress/utils';

/**
 * Internal dependencies
 */

import { Plugin } from './plugins/Plugin';

domReady( function () {
	// Attach with window to access Slider globally.
	const Slider = {
		getInstance( element, options ) {
			return createPluginInstance( element, options, Plugin );
		},

		initWith( el, options ) {
			const instance = this.getInstance( el, options );

			for ( const { element, removeEvents } of instance ) {
				element.addEventListener( 'destroy', removeEvents );
			}
		},

		init( options ) {
			const instance = this.getInstance( '.inp', options );
			for ( const { element, removeEvents } of instance ) {
				element.addEventListener( 'destroy', removeEvents );
			}
		},

		destroyWith( el ) {
			for ( const { destroy } of this.getInstance( el ) ) {
				destroy();
			}
		},

		destroy() {
			for ( const { destroy } of this.getInstance( '.inp' ) ) {
				destroy();
			}
		},
	};

	// Slider.init()
	// Slider.destroy()

	//////

	// If you do not want to attach Slider to window. use event.
	document.addEventListener( 'slider_init_with_options', ( event ) => {
		const defaultSettings = { pointerSize: 30 };
		const settings = { ...defaultSettings, ...event.detail?.settings };

		Slider.init( settings );
	} );

	const slider_init_with_options = new CustomEvent(
		'slider_init_with_options',
		{
			detail: {
				settings: {
					pointerSize: 80,
				},
			},
		}
	);

	// document.dispatchEvent(slider_init_with_options)

	document.addEventListener( 'slider_init', ( event ) => {
		Slider.init();
	} );

	document.dispatchEvent( new Event( 'slider_init' ) );

	document.addEventListener( 'slider_destroy', ( event ) => {
		Slider.destroy();
	} );

	// document.dispatchEvent(new Event('slider_destroy')) //  run when you want to destroy slider instances
} );
