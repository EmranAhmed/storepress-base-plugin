const [
	defaultJSConfig,
	defaultModuleConfig,
] = require( '@wordpress/scripts/config/webpack.config' );
const {
	requestToExternal,
	requestToHandle,
	requestToExternalModule,
	getFile,
	getWebPackAlias,
} = require( './tools/webpack-helpers' );
const WoocommerceDependencyExtractionWebpackPlugin = require( '@woocommerce/dependency-extraction-webpack-plugin' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

const scriptConfig = {
	...defaultJSConfig,
	entry: {
		...defaultJSConfig.entry(),
		'editor-scripts': getFile( 'editor-scripts.js' ),
	},

	resolve: {
		...defaultJSConfig.resolve,
		alias: {
			...defaultJSConfig.resolve.alias,
			...getWebPackAlias(),
		},
	},

	plugins: [
		// ...defaultJSConfig.plugins,
		...defaultJSConfig.plugins.filter(
			( plugin ) =>
				plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new WoocommerceDependencyExtractionWebpackPlugin( {
			requestToExternal,
			requestToHandle,
			requestToExternalModule,
		} ),

		// Removes the empty `.js` files generated by webpack but
		// sets it after WP has generated its `*.asset.php` file.
		new RemoveEmptyScriptsPlugin( {
			stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
		} ),
	],
	/*optimization: {
		splitChunks: {
			chunks: 'all',
			minSize: 1,
			name: 'common',
		},
		// runtimeChunk: { name: 'utils' },
	},*/
};

const moduleConfig = {
	...defaultModuleConfig,
	output: { ...defaultModuleConfig.output, module: true },
	experiments: { outputModule: true },
	entry: {
		...defaultModuleConfig.entry(),
		// 'custom-module': getFile( 'custom-module.js' ),
	},

	resolve: {
		...defaultModuleConfig.resolve,
		alias: {
			...defaultModuleConfig.resolve.alias,
			...getWebPackAlias(),
		},
	},

	plugins: [
		// ...defaultModuleConfig.plugins,
		...defaultModuleConfig.plugins.filter(
			( plugin ) =>
				plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new DependencyExtractionWebpackPlugin( {
			requestToExternal,
			requestToHandle,
			requestToExternalModule,
		} ),

		// Removes the empty `.js` files generated by webpack but
		// sets it after WP has generated its `*.asset.php` file.
		new RemoveEmptyScriptsPlugin( {
			stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
		} ),
	],
};

module.exports = [ scriptConfig, moduleConfig ];
