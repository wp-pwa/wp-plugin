module.exports = {
  presets: [
    [
      '@babel/env',
      {
        targets: {
          browsers: ['android >= 5', 'ios_saf > 9', 'and_chr >= 40'],
        },
        useBuiltIns: 'entry',
      },
    ],
    '@babel/react',
  ],
  plugins: [
    'styled-components',
    '@babel/proposal-object-rest-spread',
    '@babel/proposal-class-properties',
  ],
};
