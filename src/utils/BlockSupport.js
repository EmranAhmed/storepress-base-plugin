/**
 * WordPress dependencies
 */
import {
	__experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles,
	__experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
	__experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
	__experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
} from '@wordpress/block-editor';

import { getCSSRules } from '@wordpress/style-engine';

/**
 * External dependencies
 */
import clsx from 'clsx';

// array( 'background', 'color', 'spacing', 'border', 'shadow', 'dimensions' );

function getInlineStyles( styles = {} ) {
	const output = {};
	getCSSRules( styles ).forEach( ( rule ) => {
		output[ rule.key ] = rule.value;
	} );
	return output;
}

function getDimensionsClassesAndStyles( attributes ) {
	const className = clsx( {
		'has-aspect-ratio': !! attributes?.style?.dimensions?.aspectRatio,
	} );

	const inlineStyleOverrides = {};
	if ( attributes?.style?.dimensions?.aspectRatio ) {
		inlineStyleOverrides.minHeight = 'unset';
	} else if (
		attributes?.minHeight ||
		attributes?.style?.dimensions?.minHeight
	) {
		inlineStyleOverrides.aspectRatio = 'unset';
	}

	return {
		className,
		style: getInlineStyles( { dimensions: inlineStyleOverrides } ),
	};
}

function getBackgroundClassesAndStyles( attributes ) {
	const BACKGROUND_BLOCK_DEFAULT_VALUES = {
		backgroundSize: 'cover',
		backgroundPosition: '50% 50%',
		// used only when backgroundSize is 'contain'.
	};

	const hasBackgroundImage = () => {
		return (
			!! attributes?.style?.background?.backgroundImage?.id || // Supports url() string values in theme.json.
			typeof attributes?.style?.background?.backgroundImage ===
				'string' ||
			!! attributes?.style?.background?.backgroundImage?.url
		);
	};

	const setBackgroundStyleDefaults = () => {
		if (
			! attributes?.style?.background ||
			! attributes?.style?.background?.backgroundImage?.url
		) {
			return;
		}
		let backgroundStylesWithDefaults;
		if ( ! attributes?.style?.background?.backgroundSize ) {
			backgroundStylesWithDefaults = {
				backgroundSize: BACKGROUND_BLOCK_DEFAULT_VALUES.backgroundSize,
			};
		}
		if (
			attributes?.style?.background?.backgroundSize === 'contain' &&
			! attributes?.style?.background?.backgroundPosition
		) {
			backgroundStylesWithDefaults = {
				backgroundPosition:
					BACKGROUND_BLOCK_DEFAULT_VALUES.backgroundPosition,
			};
		}

		return {
			...attributes.style.background,
			...backgroundStylesWithDefaults,
		};
	};

	const backgroundStyles = setBackgroundStyleDefaults();

	if ( ! backgroundStyles ) {
		return { className: '', style: {} };
	}

	const className = clsx( {
		'has-background': !! hasBackgroundImage(),
	} );

	return {
		className,
		style: getInlineStyles( { background: backgroundStyles } ),
	};
}

function clsToObj( className ) {
	return Object.fromEntries(
		[ ...new Set( className?.split( /\s+/ ) ) ].map( ( cls ) => [
			cls,
			true,
		] )
	);
}

export const blockSupports = {
	getBackgroundStyle: ( attributes ) => {
		const { className, style } =
			getBackgroundClassesAndStyles( attributes );
		const classes = clsToObj( className );
		return { className, style, classes };
	},

	getColorStyle: ( attributes ) => {
		const { className, style } = getColorClassesAndStyles( attributes );
		const classes = clsToObj( className );
		return { className, style, classes };
	},

	getSpacingStyle: ( attributes ) => {
		const { className = '', style } =
			getSpacingClassesAndStyles( attributes );

		const classes = clsToObj( className );
		return { className, style, classes };
	},

	getBorderStyle: ( attributes ) => {
		const { className = '', style } =
			getBorderClassesAndStyles( attributes );

		const classes = clsToObj( className );
		return { className, style, classes };
	},

	getShadowStyle: ( attributes ) => {
		const { className = '', style } =
			getShadowClassesAndStyles( attributes );

		const classes = clsToObj( className );

		return { className, style, classes };
	},

	getDimensionsStyle: ( attributes ) => {
		const { className = '', style } =
			getDimensionsClassesAndStyles( attributes );

		const classes = clsToObj( className );

		return { className, style, classes };
	},
};

export function blockPropsWithoutBackground( blockProps ) {
	if ( blockProps?.style?.backgroundImage ) {
		blockProps.style.backgroundImage = null;
		blockProps.style.backgroundSize = null;

		delete blockProps.style.backgroundImage;
		delete blockProps.style.backgroundSize;
	}

	if ( blockProps?.style?.backgroundAttachment ) {
		blockProps.style.backgroundAttachment = null;
		delete blockProps.style.backgroundAttachment;
	}
	if ( blockProps?.style?.backgroundPosition ) {
		blockProps.style.backgroundPosition = null;
		delete blockProps.style.backgroundPosition;
	}
	if ( blockProps?.style?.backgroundRepeat ) {
		blockProps.style.backgroundRepeat = null;
		delete blockProps.style.backgroundRepeat;
	}

	return blockProps;
}

export function getBlockSupportStyles(
	attributes,
	features = [
		'background',
		'color',
		'spacing',
		'border',
		'shadow',
		'dimensions',
	]
) {
	const classNames = new Map();
	const styles = new Map();

	features.forEach( ( type ) => {
		const fn = type.charAt( 0 ).toUpperCase() + type.slice( 1 );
		const fnName = `get${ fn }Style`;

		const { classes, style } = blockSupports[ fnName ]( attributes );

		Object.entries( classes ).forEach( ( [ key, value ] ) => {
			if ( ! key ) {
				return;
			}
			classNames.set( key, value );
		} );
		Object.entries( style ).forEach( ( [ key, value ] ) =>
			styles.set( key, value )
		);
	} );

	return {
		className: Object.fromEntries( classNames ),
		style: Object.fromEntries( styles ),
	};
}
