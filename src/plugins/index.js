'use strict';
/**
 * External dependencies
 */
import { createStorePressPlugin } from '@storepress/utils';

/**
 * Internal dependencies
 */
import { Plugin } from '@plugins';

const StorePressSlider = createStorePressPlugin( {
	selector: '[data-storepress-slider]',
	plugin: Plugin,
	namespace: 'storepress-slider',
	options: {}, // Site-wide overrides; plugin defaults live in DEFAULTS.
} );

// Adds the init, destroy and reload event listeners.
StorePressSlider.setup();

// Optional hooks: `this` is the manager.
// createStorePressPlugin( { ..., callback: { onSetup() {}, onClear() {} } } );

export default StorePressSlider;
