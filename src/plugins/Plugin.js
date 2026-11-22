'use strict';
/**
 * External dependencies
 */
import { deepMerge, getOptionsFromAttribute } from '@storepress/utils';

// Default settings for every instance.
const DEFAULTS = {
	x: 'XXX',
};

// Values the user must not override from options or attributes.
const PRIVATE = {};

const ATTRIBUTE = 'storepress-slider'; // data-storepress-slider

// Classes and CSS custom properties the plugin adds, so reset() can remove them.
const CSS_CLASSES = [ 'storepress-slider-top', 'storepress-slider-bottom' ];

const CSS_PROPERTIES = [ '--_slider-position' ];

function Plugin( element, options ) {
	// Later sources win: DEFAULTS < manager options < data-* attributes < PRIVATE.
	const settings = deepMerge(
		DEFAULTS,
		options,
		getOptionsFromAttribute( element, ATTRIBUTE ),
		PRIVATE
	);

	// One signal removes every listener this instance adds.
	const controller = new AbortController();

	const removeStyles = () => {
		CSS_PROPERTIES.forEach( ( property ) =>
			element.style.removeProperty( property )
		);
	};

	const removeClasses = () => {
		element.classList.remove( ...CSS_CLASSES );
	};

	const removeEvents = () => {
		controller.abort();
	};

	const calculatePosition = () => {
		removeStyles();
		removeClasses();

		// Read layout values when they are needed, not once at setup.
		const { defaultView } = element.ownerDocument;
		const isRTL =
			defaultView.getComputedStyle( element ).direction === 'rtl';
		// Calculate position.
	};

	const addEvents = () => {
		const { signal } = controller;
		element.addEventListener( 'pointerenter', calculatePosition, {
			signal,
		} );
		element.addEventListener( 'focusin', calculatePosition, { signal } );
	};

	/**
	 * Resets the plugin: clears applied styles and classes and removes listeners.
	 * Called by the plugin manager on destroy, so the name must stay `reset`.
	 */
	const reset = () => {
		removeStyles();
		removeClasses();
		removeEvents();
	};

	addEvents();

	// Do not use `element` or `destroy` as keys: the manager sets them.
	return { reset, settings };
}

export { Plugin };
