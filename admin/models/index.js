import { types } from "mobx-state-tree";
import General from "./general";
import Settings from "./settings";
import Request from "./request";
import Validations from "./validations";
import Languages from "./languages";

export default types.model("Stores", {
  general: types.optional(General, {}),
  settings: types.optional(Settings, {}),
  request: types.optional(Request, {}),
  validations: types.optional(Validations, {}),
  languages: types.optional(Languages, {}),
});
