const connect = require('simple-git/promise');

(async () => {
    try {
        const repo = await connect('.');
        const remotes = await repo.getRemotes(true);
        const originPush = remotes[0].refs.push;
        const authOrigin = originPush.replace('https://', `https://${process.env.GH_TOKEN}@`)
        await repo.checkout('dev');
        await repo.raw(['rebase', '--root', 'dev', '--onto', 'master']);
        await repo.push('origin', 'dev', `--repo=${authOrigin}`);
        await repo.push('origin', 'master', `--repo=${authOrigin}`);
        console.log('Rebase finished.');
      }
      catch (error) {
        console.error('Error occurred:', error);
      }
})()