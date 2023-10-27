/**
 * External dependencies
 */
import { Icon, plus } from '@wordpress/icons';

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

import classNames from 'classnames';

function SliderPointers({ attributes }) {
	const { id } = attributes;

	// const pointerClasses = classNames({ pointer: true, pointer__item: true });

	const blockProps = useBlockProps.save();

	const { children } = useInnerBlocksProps.save();

	return (
		<div className="storepress-hotspot-slider-item" data-slider_id={id}>
			<div {...blockProps}>{children}</div>
		</div>
	);
}

function PopupPointers({ attributes }) {
	const { x, y, tooltipPosition, tooltipWidth, isOpenedDefault } = attributes;

	const pointerClasses = classNames({
		pointer: true,
		pointer__item: true,
		active: isOpenedDefault,
	});

	const pointerStyles = {
		'--left': `${x}%`,
		'--top': `${y}%`,
	};

	const tooltipClasses = classNames(
		{
			'hotspot-tooltip': true,
		},
		tooltipPosition
	);

	const blockProps = useBlockProps.save({
		className: tooltipClasses,
		style: {
			'--left': `${x}%`,
			'--top': `${y}%`,
			'--tooltip-width': `${tooltipWidth}px`,
		},
	});

	const { children } = useInnerBlocksProps.save();

	return (
		<>
			<button className={pointerClasses} style={pointerStyles}>
				<Icon icon={plus} />
			</button>
			<div {...blockProps}>{children}</div>
		</>
	);
}

export default function Save({ attributes }) {
	return attributes.parent === 'slider' ? (
		<SliderPointers attributes={attributes} />
	) : (
		<PopupPointers attributes={attributes} />
	);
}
