/* eslint-disable no-console */
const argv = require("minimist")(process.argv.slice(2));
const connect = require("simple-git/promise");
const replace = require("replace-in-file");
const packageJson = require("./package.json");

const success = async () => {
  try {
    const repo = await connect(".");
    const remotes = await repo.getRemotes(true);
    const originPush = remotes[0].refs.push;
    const authOrigin = originPush.replace(
      "https://",
      `https://${process.env.GH_TOKEN}@`
    );
    await repo.checkout("dev");
    await repo.raw(["rebase", "--root", "dev", "--onto", "master"]);
    await repo.push("origin", "dev", `--repo=${authOrigin}`);
    await repo.push("origin", "master", `--repo=${authOrigin}`);
    console.log("Rebase finished.");
  } catch (error) {
    console.error("Error occurred:", error);
  }
};

const prepare = async () => {
  const options = {
    files: "wp-pwa.php",
    from: [
      /Version: \d+\.\d+\.\d+/,
      /define\('FRONTITY_VERSION', '\d+\.\d+\.\d+'\)/,
    ],
    to: [
      `Version: ${packageJson.version}`,
      `define('FRONTITY_VERSION', '${packageJson.version}')`,
    ],
  };
  try {
    const changes = await replace(options);
    console.log("Modified files:", changes.join(", "));
  } catch (error) {
    console.error("Error occurred:", error);
  }
};

(async () => {
  if (argv.success) await success();
  else if (argv.prepare) await prepare();
})();
