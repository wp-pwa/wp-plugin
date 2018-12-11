import { types } from "mobx-state-tree";
import General from "./general";
import Ui from "./ui";
import Settings from "./settings";

export default types.model("Stores", {
  general: General,
  ui: Ui,
  settings: Settings
});
