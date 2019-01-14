import { types } from "mobx-state-tree";
import languages from "../languages";

export default types
  .model("Languages")
  .props({
    default: types.frozen(languages.en_US),
    code: types.optional(types.string, "en_US"),
  })
  .views(self => ({
    get current() {
      return languages[self.code] || {};
    },
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
  }));
