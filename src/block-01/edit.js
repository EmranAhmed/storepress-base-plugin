/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { SearchList } from '@storepress/components';

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

import {
	useState,
	useRef,
	useEffect
} from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import './editor.scss';

export default function Edit({attributes, setAttributes}) {

	const [sugessionList, setSugessionList] = useState([])
	const [loding, setLoading]              = useState(true)

	const blockProps = useBlockProps();

	const innerBlockProps = useInnerBlocksProps();

	// Fetch Attributes
	useEffect(() => {
		apiFetch({
			url : 'http://sites.local/woo-variation-swatches/wp-json/wc/store/v1/products/attributes',
			// path : 'wc/store/v1/products/attributes',
		}).then((result) => {
			if (result && result.length > 0) {
				setLoading(false)
				setSugessionList(result)
			}
		})
	}, []);

	return (
		<div {...blockProps}>
			<SearchList
				selected={['17', '23', '36']}
				valueName='name'
				isMultiSelect={true}
				isLoading={loding}

				onSearch={(input) => {
					console.log(input)
				}}
				onSelect={(item) => {
					console.log(item)
				}}
				suggestions={sugessionList}
			/>
			<div {...innerBlockProps} />
		</div>
	);
}
