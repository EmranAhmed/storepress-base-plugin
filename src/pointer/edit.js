/**
 * External dependencies
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
 * Internal dependencies
 */
import './editor.scss';

function PointerInspector({ attributes, setAttributes }) {
	const { parent, tooltipPosition, tooltipWidth, isOpenedDefault } =
		attributes;

	const isSlider = parent === 'slider';
	const isPopup = !isSlider;

	function onChangeTooltipPosition(newValue) {
		setAttributes({ tooltipPosition: newValue });
	}

	function onChangeTooltipwidth(newValue) {
		setAttributes({ tooltipWidth: newValue });
	}

	function toggleOpenedDefault(newValue) {
		setAttributes({ isOpenedDefault: newValue });
	}

	return (
		isPopup && (
			<InspectorControls>
				<PanelBody
					title={__('Tooltip Settings', 'image-hotspot-blocks')}
				>
					<ToggleControl
						label={__('Opened by default', 'image-hotspot-blocks')}
						help={__(
							'Make this tooltip opened by default',
							'image-hotspot-blocks'
						)}
						checked={isOpenedDefault}
						onChange={toggleOpenedDefault}
					/>

					<ToggleGroupControl
						label={__('Tooltip Position', 'image-hotspot-blocks')}
						value={tooltipPosition}
						isBlock
						onChange={onChangeTooltipPosition}
					>
						<ToggleGroupControlOption
							value="top"
							label={__('Top', 'image-hotspot-blocks')}
						/>
						<ToggleGroupControlOption
							value="right"
							label={__('Right', 'image-hotspot-blocks')}
						/>
						<ToggleGroupControlOption
							value="bottom"
							label={__('Bottom', 'image-hotspot-blocks')}
						/>
						<ToggleGroupControlOption
							value="left"
							label={__('Left', 'image-hotspot-blocks')}
						/>
					</ToggleGroupControl>

					<RangeControl
						label={__('Tooltip Width', 'image-hotspot-blocks')}
						value={tooltipWidth}
						onChange={onChangeTooltipwidth}
						withInputField={true}
						min={100}
						step={10}
						max={600}
					/>
				</PanelBody>
			</InspectorControls>
		)
	);
}

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({
		className: 'hotspot-pointer',
		style: {
			'--tooltip-width': `${attributes.tooltipWidth / 6}%`, // Tooltip with for editor use only. (600 max width / 100 = {tooltipWidth})
		},
	});

	const innerBlockProps = useInnerBlocksProps({
		className: 'storepress-pointer-item__inner-container-wrapper',
	});

	return (
		<>
			<PointerInspector
				attributes={attributes}
				setAttributes={setAttributes}
			/>

			<div {...blockProps}>
				<div {...innerBlockProps} />
			</div>
		</>
	);
}
