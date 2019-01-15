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
    html_purifier_active: true,
    excludes: types.array(types.string),
    api_filters: types.array(types.string),
  })
  .views(self => ({
    get root() {
      return getParent(self, 1);
    },
    get general() {
      return self.root.general;
    },
    get validations() {
      return self.root.validations.settings;
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
      if (self.validations.site_id) self.validations.clear("site_id");
      self.saveSettings();
    },
    trimTextFields() {
      self.site_id = self.site_id.trim();
      self.ssr_server = self.ssr_server.trim();
      self.static_server = self.static_server.trim();
      self.amp_server = self.amp_server.trim();
      self.excludes = self.excludes
        .map(exclude => exclude.trim())
        .filter(exclude => exclude);
      self.api_filters = self.api_filters
        .map(filter => filter.trim())
        .filter(filter => filter);
    },
    async saveSettings(event) {
      if (event) event.preventDefault();

      self.trimTextFields();

      const clientSettings = getSnapshot(self);

      if (self.validate()) {
        self.general.setSaveButtonStatus("busy");

        const data = new window.FormData();
        data.append("action", "frontity_save_settings");
        data.append("data", JSON.stringify(clientSettings));

        await post(window.ajaxurl, data);

        window.frontity.plugin.settings = clientSettings;

        setTimeout(() => {
          self.general.setSaveButtonStatus("done");
          setTimeout(() => {
            self.general.setSaveButtonStatus("idle");
          }, 1000);
        }, 500);
      } else {
        const pluginSettings = window.frontity.plugin.settings;
        const settingsWithoutValidation = [
          "site_id_requested",
          "pwa_active",
          "amp_active",
        ]
          .filter(
            setting => clientSettings[setting] !== pluginSettings[setting]
          )
          .reduce((result, setting) => {
            result[setting] = clientSettings[setting];
            return result;
          }, {});

        if (!Object.keys(settingsWithoutValidation).length) return;

        const mergedSettings = {
          ...pluginSettings,
          ...settingsWithoutValidation,
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
