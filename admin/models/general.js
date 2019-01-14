import { types } from "mobx-state-tree";

export default types
  .model("General")
  .props({
    site: "",
    page: "",
    siteIdJustRequested: false,
    saveButtonStatus: "idle",
  })
  .actions(self => ({
    setSaveButtonStatus(value) {
      self.saveButtonStatus = value;
    },
    setSiteIdJustRequested(value) {
      self.siteIdJustRequested = value;
    },
  }));
