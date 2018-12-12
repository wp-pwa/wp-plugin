import { types, getParent } from "mobx-state-tree";

export default types
  .model("UI", {
    siteIdValid: false,
    siteIdInvalid: false,
    siteIdJustRequested: false,
    requestFormName: "",
    requestFormEmail: "",
    requestFormUrl: "",
    requestFormType: "",
    requestFormTraffic: "",
  })
  .actions(self => ({
    setSiteIdJustRequested() {
      self.siteIdJustRequested = true;

      const { settings } = getParent(self, 1);

      settings.setSiteIdRequested(true);
      settings.saveSettings();
    },
    unsetSiteIdJustRequested() {
      self.siteIdJustRequested = false;
    },
    setSiteIdValid(value) {
      self.siteIdValid = value;
    },
    setSiteIdInvalid(value) {
      self.siteIdInvalid = value;
    },
    setRequestFormName({ target }) {
      self.requestFormName = target.value;
    },
    setRequestFormEmail({ target }) {
      self.requestFormEmail = target.value;
    },
    setRequestFormUrl({ target }) {
      self.requestFormUrl = target.value;
    },
    setRequestFormType({ target }) {
      self.requestFormType = target.name;
    },
    setRequestFormTraffic({ target }) {
      self.requestFormTraffic = target.name;
    },
  }));
