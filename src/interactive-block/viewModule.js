'use strict';

/**
 * WordPress dependencies
 */
import { store, getContext } from '@wordpress/interactivity';

store( 'storepress', {
	actions: {
		toggle: () => {
			const context = getContext();
			context.isOpen = ! context.isOpen;
		},
	},
	callbacks: {
		logIsOpen: () => {
			const { isOpen } = getContext();
			// Log the value of `isOpen` each time it changes.
			window.console.log( `Is open: ${ isOpen }` );
		},
	},
} );
