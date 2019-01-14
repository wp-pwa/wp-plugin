import { types, getParent } from "mobx-state-tree";
import { isEmpty, isURL } from "validator";

export default types
  .model("Settings")
  .props({
    site_id: types.maybe(types.enumeration(["valid", "invalid"])),
    ssr_server: types.maybe(types.enumeration(["valid", "invalid"])),
    static_server: types.maybe(types.enumeration(["valid", "invalid"])),
    amp_server: types.maybe(types.enumeration(["valid", "invalid"])),
  })
  .views(self => {
    const isUrl = url => isURL(url, { require_tld: false });

    return {
      get settings() {
        return getParent(self, 2).settings;
      },
      get site_idIsValid() {
        return !isEmpty(self.settings.site_id);
      },
      get ssr_serverIsValid() {
        return isUrl(self.settings.ssr_server);
      },
      get static_serverIsValid() {
        return isUrl(self.settings.static_server);
      },
      get amp_serverIsValid() {
        return isUrl(self.settings.amp_server);
      },
    };
  })
  .actions(self => ({
    clear(field) {
      self[field] = undefined;
    },
    validate(field) {
      const value = self[`${field}IsValid`];
      self[field] = value ? "valid" : "invalid";
      return value;
    },
    validateAll() {
      return [
        self.validate("site_id"),
        self.validate("ssr_server"),
        self.validate("static_server"),
        self.validate("amp_server"),
      ].every(v => v);
    },
  }));
