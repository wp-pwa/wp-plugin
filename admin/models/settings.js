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
    api_filters: types.array(types.string),
  })
  .views(self => ({
    get validations() {
      return getParent(self, 1).validations.settings;
    },
  }))
  .actions(self => ({
    setSiteId({ target }) {
      self.site_id = target.value;
      if (self.validations.site_id) self.validations.clear("site_id");
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
      if (self.validations.ssr_server) self.validations.clear("ssr_server");
    },
    setStaticServer({ target }) {
      self.static_server = target.value;
      if (self.validations.static_server)
        self.validations.clear("static_server");
    },
    setAmpServer({ target }) {
      self.amp_server = target.value;
      if (self.validations.amp_server) self.validations.clear("amp_server");
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
    setSiteIdRequested(value) {
      self.site_id_requested = value;
      self.saveSettings();
      self.validations.clear("site_id");
    },
    async saveSettings(event) {
      if (event) event.preventDefault();

      const clientSettings = getSnapshot(self);

      if (self.validate()) {
        const data = new window.FormData();
        data.append("action", "frontity_save_settings");
        data.append("data", JSON.stringify(clientSettings));

        await post(window.ajaxurl, data);

        window.frontity.plugin.settings = clientSettings;
      } else {
        const pluginSettings = window.frontity.plugin.settings;
        const settingsWithoutValidation = [
          "site_id_requested",
          "pwa_active",
          "amp_active",
        ].filter(
          setting => clientSettings[setting] !== pluginSettings[setting]
        );

        if (!settingsWithoutValidation.length) return;

        const mergedSettings = {
          ...pluginSettings,
          ...settingsWithoutValidation.reduce((result, setting) => {
            result[setting] = clientSettings[setting];
            return result;
          }, {}),
        };

        const data = new window.FormData();
        data.append("action", "frontity_save_settings");
        data.append("data", JSON.stringify(mergedSettings));

        await post(window.ajaxurl, data);

        window.frontity.plugin.settings = mergedSettings;
      }
    },
    async purgeHtmlPurifierCache() {
      const data = new window.FormData();
      data.append("action", "frontity_purge_htmlpurifier_cache");

      await post(window.ajaxurl, data);
    },
    validate() {
      return self.validations.validateAll();
    },
  }));
