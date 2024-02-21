/**
 * External dependencies
 */
import { getOptionsFromAttribute } from '@storepress/utils';

function Plugin( element, options ) {
	// Default Settings
	const DEFAULTS = {
		pointerSize: 20,
	};

	// Collecting settings from html attribute
	const ATTRIBUTE = 'slider-settings';

	// Do what you need and return expose fn.
	const init = () => {
		this.element = element;
		this.settings = Object.assign(
			{},
			DEFAULTS,
			options,
			getOptionsFromAttribute( this.element, ATTRIBUTE )
		);
		//  console.log('init')
		addEvents();

		return expose();
	};

	const addPointer = ( event ) => {
		window.console.log( event.target.value );
	};

	const addEvents = () => {
		this.element.querySelectorAll( 'input' ).forEach( ( el ) => {
			el.addEventListener( 'input', addPointer );
		} );
	};

	const removeEvents = () => {
		this.element.querySelectorAll( 'input' ).forEach( ( el ) => {
			el.removeEventListener( 'input', addPointer );
		} );
	};

	// Expose to public.
	const expose = () => ( {
		element: this.element,
		removeEvents,
	} );

	return ( () => init() )();
}

export { Plugin };
