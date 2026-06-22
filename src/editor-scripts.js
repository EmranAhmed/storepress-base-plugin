/**
 * WordPress dependencies
 */
import { Icon } from '@wordpress/icons';
import { updateCategory } from '@wordpress/blocks';
/**
 * External dependencies
 */
import { storepress } from '@storepress/icons';

( function () {
	updateCategory( 'storepress', { icon: <Icon icon={ storepress } /> } );
} )();
