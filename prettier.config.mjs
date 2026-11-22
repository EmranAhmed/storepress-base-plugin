import baseConfig from '@wordpress/prettier-config';

/** @type {import('prettier').Config} */
export default {
	...baseConfig,
	overrides: [
		...( baseConfig.overrides ?? [] ),
		{
			files: [ 'changelog.txt' ],
			options: { parser: 'markdown' },
		},
	],
};
