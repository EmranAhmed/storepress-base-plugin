const defaultConfig = require("@wordpress/jest-preset-default/jest-preset.js");

// @see https://github.com/WordPress/gutenberg/blob/trunk/test/unit/jest.config.js
// @see https://github.com/woocommerce/woocommerce/blob/trunk/plugins/woocommerce-blocks/tests/js/jest.config.json

const config = {
	...defaultConfig,
	rootDir: "./",
	testPathIgnorePatterns: [
		"<rootDir>/node_modules/",
		"<rootDir>/vendor/",
		"<rootDir>/build/",
	],
	testMatch: [
		'<rootDir>/tests/js/**/?(*.)+(test).[jt]s?(x)'
	],
	transform: {
		"^.+\\.(js|ts|tsx)$": "<rootDir>/tests/js/jestPreprocess.js"
	},
	verbose: true,
};

module.exports = config;
