const path = require('path');
const webpack = require('webpack');

new webpack.HotModuleReplacementPlugin();

module.exports = {
	entry: {
		'wiki': ['@babel/polyfill', './src/wiki.index.js'],
		'authentication': ['@babel/polyfill', './src/authentication.index.js'],
		'admin': ['@babel/polyfill', './src/admin.index.js']
	},
	output: {
		filename: '[name].bundle.js',
		path: path.resolve(__dirname, '..', 'public', 'js')
	},
	module: {
		rules: [
			{
				test: /\.(js|jsx|tsx|ts)$/,
				resolve: { 
					alias: {
						Components: path.resolve(__dirname, 'src/components/'),
						Scripts: path.resolve(__dirname, 'src/scripts/'),
						Utilities: path.resolve(__dirname, 'src/utilities/')
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
		myApp: 'myApp'
	}
};
