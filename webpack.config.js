const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

// Prevent cleaning output directory.
const plugins = defaultConfig.plugins.filter(
	( plugin ) => plugin.constructor.name !== 'CleanWebpackPlugin'
);

module.exports = {
	...defaultConfig,
	entry: {
		index: './resources/index.js',
		settings: './resources/settings.js',
		layout: './resources/layout.js',
	},
	output: {
		...defaultConfig.output,
		filename: '[name].js',
		chunkFilename: '[name].js',
		path: require( 'path' ).resolve( __dirname, 'assets' ),
		clean: false,
	},
	optimization: {
		...defaultConfig.optimization,
		splitChunks: false,
	},
	// Use filtered plugins
	plugins: [ ...plugins ],
	cache: {
		type: 'filesystem',
		buildDependencies: {
			config: [ __filename ],
		},
	},
};
