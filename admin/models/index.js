import { types } from "mobx-state-tree";
import General from "./general";
import Ui from "./ui";
import Settings from "./settings";
import Languages from "./languages";

export default types.model("Stores", {
  general: General,
  ui: Ui,
  settings: Settings,
  languages: types.optional(Languages, {}),
});
