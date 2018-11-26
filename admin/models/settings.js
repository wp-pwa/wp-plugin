import { types } from "mobx-state-tree";

export default types.model("Settings", {
  title: "Admin",
  synced_with_wp_pwa: true,
  wp_pwa_amp: "disabled",
  wp_pwa_amp_server: "https://amp.wp-pwa.com",
  wp_pwa_env: "prod",
  wp_pwa_excludes: types.array(types.string),
  wp_pwa_force_frontpage: true,
  wp_pwa_siteid: "",
  wp_pwa_ssr: "https://ssr.wp-pwa.com",
  wp_pwa_static: "https://static.wp-pwa.com",
  wp_pwa_status: "disabled"
});
