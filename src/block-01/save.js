'use strict';
/**
 * External dependencies
 */
import { Icon, plus } from '@wordpress/icons';

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

import classNames from 'classnames';

function StaticBlock01({ attributes }) {
	const { x } = attributes;

	const classes = classNames({ 'class-01': true });

	const blockProps = useBlockProps.save({
		className: classes,
		style: {
			'--css-variable': `${x}%`,
		},
	});

	const { children } = useInnerBlocksProps.save();

	return (
		<div {...blockProps}>
			<h1>StorePress Base Block - save.js</h1>
			{children}
		</div>
	);
}

function StaticBlock02({ attributes }) {
	const { x } = attributes;

	const classes = classNames({ 'class-01': true });

	const blockProps = useBlockProps.save({
		className: classes,
		style: {
			'--css-variable': `${x}%`,
		},
	});

	const innerBlocksProps = useInnerBlocksProps.save(blockProps);

	return <div {...innerBlocksProps} />;
}

function DynamicBlock({ attributes }) {
	const { x } = attributes;

	const classes = classNames({ 'class-01': true });

	const blockProps = useBlockProps.save({
		className: classes,
		style: {
			'--css-variable': `${x}%`,
		},
	});

	const { children } = useInnerBlocksProps.save(blockProps);
	const innerBlocksProps = useInnerBlocksProps.save(blockProps);

	return children;
}

export default function Save({ attributes }) {
	return <DynamicBlock attributes={attributes} />;
}
