import { types, getParent } from "mobx-state-tree";
import { isEmpty, isEmail, isURL } from "validator";

export default types
  .model("Request")
  .props({
    name: types.maybe(types.enumeration(["valid", "invalid"])),
    email: types.maybe(types.enumeration(["valid", "invalid"])),
    url: types.maybe(types.enumeration(["valid", "invalid"])),
    type: types.maybe(types.enumeration(["valid", "invalid"])),
    traffic: types.maybe(types.enumeration(["valid", "invalid"])),
  })
  .views(self => ({
    get request() {
      return getParent(self, 2).request;
    },
    get nameIsValid() {
      return !isEmpty(self.request.name);
    },
    get emailIsValid() {
      return isEmail(self.request.email);
    },
    get urlIsValid() {
      return isURL(self.request.url);
    },
    get typeIsValid() {
      return !isEmpty(self.request.type);
    },
    get trafficIsValid() {
      return !isEmpty(self.request.traffic);
    },
  }))
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
        self.validate("name"),
        self.validate("email"),
        self.validate("url"),
        self.validate("type"),
        self.validate("traffic"),
      ].every(v => v);
    },
  }));
