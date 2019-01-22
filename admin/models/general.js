import { types } from "mobx-state-tree";

export default types
  .model("General")
  .props({
    pluginDirUrl: "",
    page: "",
    siteIdJustRequested: false,
    saveButtonStatus: "idle",
    purgePurifierButtonStatus: "idle",
  })
  .actions(self => ({
    setSiteIdJustRequested(value) {
      self.siteIdJustRequested = value;
    },
    setSaveButtonStatus(value) {
      self.saveButtonStatus = value;
    },
    setPurgePurifierButtonStatus(value) {
      self.purgePurifierButtonStatus = value;
    },
  }));
