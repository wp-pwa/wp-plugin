import { types, getSnapshot, getParent } from "mobx-state-tree";
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
    setSiteIdRequested(value) {
      self.site_id_requested = value;
    },
    setPwaActive({ target }) {
      self.pwa_active = target.checked;
      self.saveSettings();
    },
    setAmpActive({ target }) {
      self.amp_active = target.checked;
      self.saveSettings();
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
      self.frontpage_forced = target.checked;
    },
    setHtmlPurifierActive({ target }) {
      self.html_purifier_active = target.checked;
    },
    setExcludes({ target }) {
      self.excludes = target.value.split("\n");
    },
    setApiFilters({ target }) {
      self.api_filters = target.value.split("\n");
    },
    async saveSettings() {
      const data = new window.FormData();
      data.append("action", "frontity_save_settings");
      data.append("data", JSON.stringify(getSnapshot(self)));

      await post(window.ajaxurl, data);

      window.frontity.plugin.settings = getSnapshot(self);

      const { ui } = getParent(self, 1);

      if (self.site_id.length === 17) ui.setSiteIdValid(true);
      else ui.setSiteIdInvalid(true);
    }
  }));
