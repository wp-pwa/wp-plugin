const path = require("path");
const { BundleAnalyzerPlugin } = require("webpack-bundle-analyzer");

module.exports = {
  mode: process.env.NODE_ENV || "development",
  devtool: "eval",
  entry: {
    main: "./admin/index.js",
  },
  output: {
    path: path.resolve(__dirname, "./admin/dist"),
    filename: "[name].js",
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: "babel-loader",
      },
    ],
  },
  plugins: process.env.ANALYZE
    ? [
        new BundleAnalyzerPlugin({
          analyzerMode: "static",
          reportFilename: "../analyze/production.html",
          openAnalyzer: false,
          generateStatsFile: true,
          statsFilename: "../analyze/production.json",
        }),
      ]
    : [],
};
