/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';

import {
	PanelBody,
	RangeControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	ToggleControl,
} from '@wordpress/components';

/**
 * External dependencies
 */
import { SearchListControl } from '@storepress/components';

import { useState, useRef, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { getProductsRequests } from '@utils';
import { getBlockSupportStyles } from '@block-support';
import './editor.scss';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const [ sugessionList, setSugessionList ] = useState( [] );
	const [ loading, setLoading ] = useState( true );

	/**
	 * Create an `onChange` handler that saves the new value to one attribute.
	 *
	 * @param {string} key Attribute name.
	 * @return {(value: string|number|boolean|Object) => void} Handler that stores the value it receives.
	 * @example
	 * ```js
	 * 	const { orientation } = attributes;
	 * <ToggleGroupControl
	 * 						label={ __( 'Orientation', 'default' ) }
	 * 						value={ orientation }
	 * 						onChange={ set( 'orientation' ) }
	 * 						isBlock
	 * 					/>
	 * ```
	 */
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );

	const blockProps = useBlockProps();

	const innerBlockProps = useInnerBlocksProps();

	// Fetch Attributes
	useEffect( () => {
		apiFetch( {
			url: 'http://sites.local/woo-variation-swatches/wp-json/wc/store/v1/products/attributes',
			// path : 'wc/store/v1/products/attributes',
		} ).then( ( result ) => {
			if ( result && result.length > 0 ) {
				setLoading( false );
				setSugessionList( result );
			}
		} );
	}, [ clientId ] );

	return (
		<div { ...blockProps }>
			{ /*<SearchList
				selected={['17', '23', '36']}
				valueName='name'
				isMultiSelect={true}
				isLoading={loading}

				onSearch={(input) => {
					console.log(input)
				}}
				onSelect={(item) => {
					console.log(item)
				}}
				suggestions={sugessionList}
			/>*/ }
			<h1>StorePress Base Block - edit.js</h1>
			<div { ...innerBlockProps } />
		</div>
	);
}
