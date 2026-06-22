#!/usr/bin/env node
/* eslint-disable no-console */
/**
 * External dependencies
 */
const fs = require('fs')
const spawn = require('cross-spawn')

/**
 * Internal dependencies
 */
const {
	getArgFromCLI,
} = require('@wordpress/scripts/utils')

/**
 * Constants
 */

const PACKAGES_PREFIX = ['@storepress/']
const COMPOSER_PACKAGES_PREFIX = [ 'storepress/' ];

function readJSONFile (fileName) {
	const data = fs.readFileSync(fileName, 'utf8')
	return JSON.parse(data)
}

function getStorePressPackages ({ dependencies = {}, devDependencies = {} }) {
	return Object.keys(dependencies).concat(Object.keys(devDependencies)).filter((packageName) => {
		return PACKAGES_PREFIX.some((prefix) => packageName.startsWith(prefix))
	})
}

function getStorePressComposerPackages( {
	require: req = {},
	'require-dev': reqDev = {},
} ) {
	return Object.keys( req )
		.concat( Object.keys( reqDev ) )
		.filter( ( packageName ) => {
			return COMPOSER_PACKAGES_PREFIX.some( ( prefix ) =>
				packageName.startsWith( prefix )
			);
		} );
}

function getPackageVersionDiff (initialPackageJSON, finalPackageJSON) {
	const diff = ['dependencies', 'devDependencies'].reduce(
		( result, keyPackageJSON ) => {
			return Object.keys(
				finalPackageJSON[keyPackageJSON] || {},
			).reduce((_result, dependency) => {
				const initial =
					initialPackageJSON[keyPackageJSON][dependency]
				const final = finalPackageJSON[keyPackageJSON][dependency]
				if (initial !== final) {
					_result.push({ dependency, initial, final })
				}
				return _result
			}, result)
		},
		[],
	)
	return diff.sort((a, b) => a.dependency.localeCompare(b.dependency))
}

function getComposerVersionDiff( initialComposerJSON, finalComposerJSON ) {
	const diff = [ 'require', 'require-dev' ].reduce(
		( result, keyComposerJSON ) => {
			return Object.keys(
				finalComposerJSON[ keyComposerJSON ] || {}
			).reduce( ( _result, dependency ) => {
				const initial = ( initialComposerJSON[ keyComposerJSON ] ||
					{} )[ dependency ];
				const final =
					finalComposerJSON[ keyComposerJSON ][ dependency ];
				if ( initial !== final ) {
					_result.push( { dependency, initial, final } );
				}
				return _result;
			}, result );
		},
		[]
	);
	return diff.sort( ( a, b ) => a.dependency.localeCompare( b.dependency ) );
}

function updatePackagesToLatestVersion (packages) {
	const distTag = getArgFromCLI('--dist-tag') || 'latest'

	const packagesWithLatest = packages.map(
		(packageName) => `${packageName}@${distTag}`,
	)

	return spawn.sync('npm', ['install', ...packagesWithLatest, '--save'], {
		stdio: 'inherit',
	})
}

function updateComposerPackagesToLatestVersion( packages ) {
	return spawn.sync(
		'composer',
		[ 'require', ...packages, '--with-all-dependencies' ],
		{
			stdio: 'inherit',
		}
	);
}

function outputPackageDiffReport (packageDiff) {
	console.log(
		[
			'The following package versions were changed:',
			...packageDiff.map(({ dependency, initial, final }) => {
				return `${dependency}: ${initial} -> ${final}`
			}),
		].join('\n'),
	)
}

function outputComposerDiffReport( packageDiff ) {
	if ( packageDiff.length === 0 ) {
		console.log( 'No composer package versions were changed.' );
		return;
	}

	console.log(
		[
			'The following composer package versions were changed:',
			...packageDiff.map( ( { dependency, initial, final } ) => {
				return `${ dependency }: ${ initial } -> ${ final }`;
			} ),
		].join( '\n' )
	);
}

function updatePackageJSON () {
	const initialPackageJSON = readJSONFile( 'package.json' );
	const packages = getStorePressPackages( initialPackageJSON );

	const result = updatePackagesToLatestVersion( packages );

	const finalPackageJSON = readJSONFile( 'package.json' );
	outputPackageDiffReport(
		getPackageVersionDiff( initialPackageJSON, finalPackageJSON )
	);

	return result.status;
}

function updateComposerJSON() {
	if ( ! fs.existsSync( 'composer.json' ) ) {
		console.log( 'No composer.json found, skipping composer update.' );
		return 0;
	}

	const initialComposerJSON = readJSONFile( 'composer.json' );
	const packages = getStorePressComposerPackages( initialComposerJSON );

	if ( packages.length === 0 ) {
		console.log(
			'No @storepress composer packages found, skipping composer update.'
		);
		return 0;
	}

	const result = updateComposerPackagesToLatestVersion( packages );

	const finalComposerJSON = readJSONFile( 'composer.json' );
	outputComposerDiffReport(
		getComposerVersionDiff( initialComposerJSON, finalComposerJSON )
	);

	return result.status;
}

function update() {
	const npmStatus = updatePackageJSON();
	const composerStatus = updateComposerJSON();
	process.exit( npmStatus || composerStatus );
}

update();
/* eslint-enable no-console */
