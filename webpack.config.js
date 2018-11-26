const path = require("path");

module.exports = {
  mode: process.env.NODE_ENV || "development",
  devtool: "eval",
  entry: {
    main: "./admin/index.js",
    activation: "./admin/activation.js"
  },
  output: {
    path: path.resolve(__dirname, "./admin/dist"),
    filename: "[name].js"
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        loader: "babel-loader"
      }
    ]
  }
};
