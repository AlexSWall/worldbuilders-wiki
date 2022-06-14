const path = require('path');
const webpack = require('webpack');

new webpack.HotModuleReplacementPlugin();

module.exports = {
	entry: {
		'wiki': ['@babel/polyfill', './src/wiki.index.tsx'],
		'reset-password': ['@babel/polyfill', './src/reset-password.index.tsx'],
		'administration': ['@babel/polyfill', './src/administration.index.tsx']
	},
	output: {
		filename: '[name].bundle.js',
		path: path.resolve(__dirname, '..', 'public', 'javascript')
	},
	module: {
		rules: [
			{
				test: /\.(js|jsx|ts|tsx)$/,
				resolve: { 
					alias: {
						Components: path.resolve(__dirname, 'src/Components/'),
						'Form Components': path.resolve(__dirname, 'src/Components/Form_Components/'),
						'GlobalState': path.resolve(__dirname, 'src/GlobalState.tsx'),
						'utils': path.resolve(__dirname, 'src/utils/')
					},
					extensions: ['.js', '.jsx', '.ts', '.tsx']
				}, 
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options:
					{
						'presets': ['@babel/preset-env', '@babel/preset-react'],
						'plugins': ['react-hot-loader/babel', '@babel/plugin-proposal-object-rest-spread']
					}
				}
			},
			{
				test: /\.js$/,
				use: ["source-map-loader"],
				enforce: "pre"
			},
		]
	},
	mode: 'development',
	externals: {
		globalsData: 'globalsData'
	},
	watchOptions: {
		poll: 1000,
		ignored: /node_modules/
	}
};
