import { types } from "mobx-state-tree";
import General from "./general";
import Settings from "./settings";

export default types.model("Stores", {
  general: General,
  settings: Settings
});
