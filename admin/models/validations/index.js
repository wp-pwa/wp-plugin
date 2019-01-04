import { types } from "mobx-state-tree";
import Settings from "./settings";
import Request from "./request";

export default types.model("Validations").props({
  settings: types.optional(Settings, {}),
  request: types.optional(Request, {}),
});
