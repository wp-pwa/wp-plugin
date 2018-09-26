const replace = require('replace-in-file');
const packageJson = require('../package.json');

(async () => {
    const options = {
      files: 'wp-pwa.php',
      from: [/Version: \d+\.\d+\.\d+/, /plugin_version = '\d+\.\d+\.\d+'/],
      to: [`Version: ${packageJson.version}`, `plugin_version = '${packageJson.version}'`],
    };
    try {
        const changes = await replace(options)
        console.log('Modified files:', changes.join(', '));
      }
      catch (error) {
        console.error('Error occurred:', error);
      }

})()