import { types, getSnapshot } from "mobx-state-tree";
import { post } from "axios";

export default types
  .model("Settings", {
    site_id: "",
    site_id_requested: false,
    pwa_active: false,
    amp_active: false,
    ssr_server: "",
    static_server: "",
    amp_server: "",
    frontpage_forced: false,
    html_purifier_active: false,
    excludes: types.array(types.string),
    api_filters: types.array(types.string)
  })
  .actions(self => ({
    setSiteId({ target }) {
      self.site_id = target.value;
    },
    setSiteIdRequested({ target }) {
      self.site_id_requested = target.value;
    },
    setPwaActive({ target }) {
      self.pwa_active = target.value;
    },
    setAmpActive({ target }) {
      self.amp_active = target.value;
    },
    setSsrServer({ target }) {
      self.ssr_server = target.value;
    },
    setStaticServer({ target }) {
      self.static_server = target.value;
    },
    setAmpServer({ target }) {
      self.amp_server = target.value;
    },
    setFrontpageForced({ target }) {
      self.frontpage_forced = target.value;
    },
    setHtmlPurifierActive({ target }) {
      self.html_purifier_active = target.value;
    },
    setExcludes({ target }) {
      self.excludes = target.value;
    },
    setApiFilters({ target }) {
      self.api_filters = target.value;
    },
    async saveSettings(event) {
      event.preventDefault();

      const data = new window.FormData();
      data.append("action", "frontity_save_settings");
      data.append("data", JSON.stringify(getSnapshot(self)));

      await post(window.ajaxurl, data);
    }
  }));
