const webpack = require('webpack');
const path = require('path');

module.exports = {
  mode: process.env.NODE_ENV || 'development',
  devtool: 'eval',
  entry: './admin/frontity-admin.js',
  output: {
    path: path.resolve(__dirname, './admin/dist'),
    filename: 'frontity-admin.js',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        loader: 'babel-loader',
      },
    ],
  },
};
