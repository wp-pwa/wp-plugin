import { types } from "mobx-state-tree";
import * as languages from "./languages";

export default types
  .model("Languages")
  .props({
    default: types.frozen(languages.en),
    current: types.frozen({}),
    code: types.optional(types.string, "en")
  })
  .views(self => ({
    get(key) {
      return self.current[key] || self.default[key];
    }
  }))
  .actions(self => ({
    setLanguage(language) {
      self.current = languages[language] || {};
      self.code = language;
    }
  }));
