'use strict';
/* eslint no-console: 0 */
/* eslint import/named: 0 */
/* eslint import/no-extraneous-dependencies: 0 */

/**
 * External dependencies
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
			console.log( `Is open: ${ isOpen }` );
		},
	},
} );
