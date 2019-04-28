const path = require('path');

module.exports = {
  entry: {
    app: ['@babel/polyfill', 
      './src/index.js']
  },
  output: {
    path: path.resolve(__dirname, '..', 'public', 'resources', 'wiki', 'js'),
    filename: 'wiki.bundle.js'
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
          options: {
            presets: ['@babel/preset-env', '@babel/preset-react']
          }
        }
      }
    ]
  },
  mode: 'production',
  externals: {
    myApp: 'myApp'
  }
};
