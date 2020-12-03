const path = require('path');
const webpack = require('webpack');

new webpack.HotModuleReplacementPlugin();

module.exports = {
	entry: {
		'wiki': ['@babel/polyfill', './src/wiki.index.jsx'],
		'authentication': ['@babel/polyfill', './src/authentication.index.jsx'],
		'administration': ['@babel/polyfill', './src/administration.index.jsx']
	},
	output: {
		filename: '[name].bundle.js',
		path: path.resolve(__dirname, '..', 'public', 'javascript')
	},
	module: {
		rules: [
			{
				test: /\.(js|jsx|tsx|ts)$/,
				resolve: { 
					alias: {
						Components: path.resolve(__dirname, 'src/Components/'),
						'Form Components': path.resolve(__dirname, 'src/Components/Form Components/'),
						'GlobalsContext': path.resolve(__dirname, 'src/GlobalsContext.jsx')
					},
					extensions: [".js", ".jsx"]
				}, 
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options:
					{
						"presets": ["@babel/preset-env", "@babel/preset-react"],
						"plugins": ["react-hot-loader/babel", "@babel/plugin-proposal-object-rest-spread"]
					}
				}
			}
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
