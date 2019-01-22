import { types, getSnapshot } from "mobx-state-tree";
import Settings from "./settings";
import Request from "./request";

export default types
  .model("Validations")
  .props({
    settings: types.optional(Settings, {}),
    request: types.optional(Request, {}),
  })
  .actions(self => ({
    clear(model, field) {
      self[model][field] = undefined;
    },
    validate(model, field) {
      const value = self[model][`${field}IsValid`];
      self[model][field] = value ? "valid" : "invalid";
      return value;
    },
    validateAll(model) {
      const fields = Object.keys(getSnapshot(self[model]));
      return fields.map(field => self.validate(model, field)).every(v => v);
    },
  }));
