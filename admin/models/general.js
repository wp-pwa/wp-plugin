import { types } from "mobx-state-tree";

export default types
  .model("General")
  .props({
    site: "",
    page: "",
    siteIdJustRequested: false,
  })
  .actions(self => ({
    setSiteIdJustRequested(value) {
      self.siteIdJustRequested = value;
    },
  }));
