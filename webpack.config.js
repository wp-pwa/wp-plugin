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
      {
        test: /\.(jpg|png)$/,
        use: {
          loader: "file-loader",
          options: {
            name: "[name].[hash].[ext]",
            outputPath: "assets/",
            publicPath: path.resolve(
              (__dirname.match(/\/wp-content\/.+/, "i") || [""])[0],
              "admin/dist/assets/"
            ),
          },
        },
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
