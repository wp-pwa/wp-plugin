module.exports = {
  presets: [
    [
      "@babel/env",
      {
        targets: {
          and_chr: "64",
          chrome: "64",
          ios_saf: "10",
          safari: "10"
        },
        useBuiltIns: "entry"
      }
    ],
    "@babel/react"
  ],
  plugins: [
    "styled-components",
    "@babel/proposal-object-rest-spread",
    "@babel/proposal-class-properties"
  ]
};
