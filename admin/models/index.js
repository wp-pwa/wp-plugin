import { types } from "mobx-state-tree";
import Settings from "./settings";

export default types.model("Stores", {
  settings: Settings
});
