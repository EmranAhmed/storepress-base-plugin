/**
 * External dependencies
 */
const resolverNode = require( 'eslint-import-resolver-node' );
const path = require( 'path' );

const PACKAGES_DIR = path.resolve( __dirname, '../src' );

exports.interfaceVersion = 2;

exports.resolve = function ( source, file, config ) {
	const resolve = ( sourcePath ) =>
		resolverNode.resolve( sourcePath, file, {
			...config,
			extensions: [ '.tsx', '.ts', '.mjs', '.js', '.json', '.node' ],
		} );

	// @storepress/hotspot-utils

	if ( source === '@utils' ) {
		return resolve( path.join( PACKAGES_DIR, `utils/` ) );
	}

	return resolve( source );
};
