/**
 * External dependencies
 */

/**
 * Root Jest configuration for StorePress monorepo
 * Uses @wordpress/jest-preset-default with jsdom for DOM testing
 */

const defaultConfig = require('@wordpress/scripts/config/jest-unit.config.js')

module.exports = {

	...defaultConfig,

	// Root directory
	rootDir: '.',

	// Use jsdom for DOM testing support
	// testEnvironment: 'jsdom',
	testEnvironment: 'jest-environment-jsdom',

	// Setup file to configure DOM environment before tests
	setupFilesAfterEnv: [
		'<rootDir>/jest.setup.js',
	],

	// Module file extensions
	moduleFileExtensions: [ 'js', 'jsx' ],

	// Monorepo test file patterns
	testMatch: [
		'<rootDir>/tests/js/**/?(*.)+(test).[jt]s?(x)'
	],

	// Module resolution for monorepo
	//moduleDirectories: [ 'node_modules' ],

	// Module name mapping for package imports
	moduleNameMapper: {
		// CSS/SCSS mocks
		'\\.(css|scss|sass)$': '<rootDir>/tests/js/styleMock.js',

		'\\.(jpg|jpeg|png|gif|webp|svg)$': 'identity-obj-proxy',
		//'\\.(css|scss|sass)$': 'identity-obj-proxy',
	},

	testPathIgnorePatterns: [
		"<rootDir>/node_modules/",
		"<rootDir>/vendor/",
		"<rootDir>/build/",
		'<rootDir>/.cache/',
		'<rootDir>/.git/',
		'<rootDir>/.github/',
		'<rootDir>/tools/'
	],

	// Transform ES modules
	transform: {
		'^.+\\.[jt]sx?$': [
			'babel-jest', {
				presets: ['@wordpress/babel-preset-default'],
			}],
	},

	// Handle ES modules in node_modules
	transformIgnorePatterns: [
		'/node_modules/(?!(@wordpress|@storepress|@babel/runtime)/)',
	],

	// Projects for per-package configuration (optional)
	// Uncomment to use per-package Jest configs
	// projects: [
	// 	'<rootDir>/packages/*/jest.config.js',
	// ],

	// Show verbose test results
	verbose: false,

	// Clear mock calls between tests
	clearMocks: true,

	// Reset module registry between tests
	resetModules: true,

	// Add this: Store cache locally in your project
	cacheDirectory: '<rootDir>/.cache/jest',

    // Collect coverage from packages source files only
	collectCoverageFrom: [
		'src/*.js',
		'src/**/*.js'
	],

	// Coverage output directory
	coverageDirectory: '<rootDir>/.coverage',

	// Coverage thresholds - adjust as your test coverage improves
	coverageThreshold: {
		global: {
			branches: 60,
			functions: 70,
			lines: 70,
			statements: 70,
		},
	},
}
