process.env.GIT_AUTHOR_NAME = 'Frontibotito';
process.env.GIT_AUTHOR_EMAIL = 'frontibotito@frontity.com';
process.env.GIT_COMMITTER_NAME = 'Frontibotito';
process.env.GIT_COMMITTER_EMAIL = 'frontibotito@frontity.com';

module.exports = {
  verifyConditions: [
    '@semantic-release/changelog',
    '@semantic-release/github',
    '@semantic-release/git',
  ],
  prepare: [
    '@semantic-release/changelog',
    '@semantic-release/npm',
    {
      path: '@semantic-release/exec',
      cmd: 'npm run sr:prepare',
    },
    {
      path: '@semantic-release/git',
      message: 'chore(release): ${nextRelease.version}',
      assets: ['CHANGELOG.md', 'package.json', 'package-lock.json', 'wp-pwa.php'],
    },
  ],
  publish: ['@semantic-release/github'],
  success: [
    {
      path: '@semantic-release/exec',
      cmd: 'npm run sr:success',
    },
    '@semantic-release/github',
  ],
  fail: ['@semantic-release/github'],

  branch: 'master',
  npmPublish: false,
  noCi: true,
};
