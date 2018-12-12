import { types } from "mobx-state-tree";
import * as languages from "../languages";

export default types
  .model("Languages")
  .props({
    default: types.frozen(languages.en),
    current: types.frozen({}),
    code: types.optional(types.string, "en"),
  })
  .views(self => ({
    get(key) {
      const results = key.split(".").reduce(
        (result, property) => {
          if (result[0] && result[0][property]) result[0] = result[0][property];
          else result[0] = null;
          if (result[1] && result[1][property]) result[1] = result[1][property];
          else result[1] = null;

          return result;
        },
        [self.current, self.default]
      );

      return results[0] || results[1];
    },
  }))
  .actions(self => ({
    setLanguage(language) {
      self.current = languages[language] || {};
      self.code = language;
    },
  }));
