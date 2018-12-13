import { types, getParent } from "mobx-state-tree";
import { isLength, isEmpty, isURL, isEmail } from "validator";

export default types
  .model("UI", {
    siteIdJustRequested: false,
    siteIdStatus: types.maybe(types.enumeration(["valid", "invalid"])),
    ssrServerStatus: types.maybe(types.enumeration(["valid", "invalid"])),
    staticServerStatus: types.maybe(types.enumeration(["valid", "invalid"])),
    ampServerStatus: types.maybe(types.enumeration(["valid", "invalid"])),
    requestFormName: "",
    requestFormEmail: "",
    requestFormUrl: "",
    requestFormType: "",
    requestFormTraffic: "",
    requestFormNameStatus: types.maybe(types.enumeration(["valid", "invalid"])),
    requestFormEmailStatus: types.maybe(
      types.enumeration(["valid", "invalid"])
    ),
    requestFormUrlStatus: types.maybe(types.enumeration(["valid", "invalid"])),
    requestFormTypeStatus: types.maybe(types.enumeration(["valid", "invalid"])),
    requestFormTrafficStatus: types.maybe(
      types.enumeration(["valid", "invalid"])
    ),
  })
  .actions(self => ({
    setSiteIdJustRequested(value) {
      self.siteIdJustRequested = value;
    },
    setSiteIdStatus(value) {
      self.siteIdStatus = value;
    },
    setSsrServerStatus(value) {
      self.ssrServerStatus = value;
    },
    setStaticServerStatus(value) {
      self.staticServerStatus = value;
    },
    setAmpServerStatus(value) {
      self.ampServerStatus = value;
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
    setRequestFormNameStatus(value) {
      self.requestFormNameStatus = value;
    },
    setRequestFormEmailStatus(value) {
      self.requestFormEmailStatus = value;
    },
    setRequestFormUrlStatus(value) {
      self.requestFormUrlStatus = value;
    },
    setRequestFormTypeStatus(value) {
      self.requestFormTypeStatus = value;
    },
    setRequestFormTrafficStatus(value) {
      self.requestFormTrafficStatus = value;
    },
    validateSettings() {
      const { settings } = getParent(self, 1);

      self.setSiteIdStatus(
        isLength(settings.site_id, { min: 17, max: 17 }) ? "valid" : "invalid"
      );
      self.setSsrServerStatus(isURL(settings.ssr_server) ? "valid" : "invalid");
      self.setStaticServerStatus(
        isURL(settings.static_server) ? "valid" : "invalid"
      );
      self.setAmpServerStatus(isURL(settings.amp_server) ? "valid" : "invalid");
    },
    validateRequestForm() {
      self.setRequestFormNameStatus(
        !isEmpty(self.requestFormName, { ignore_whitespace: true })
          ? "valid"
          : "invalid"
      );
      self.setRequestFormEmailStatus(
        isEmail(self.requestFormEmail) ? "valid" : "invalid"
      );
      self.setRequestFormUrlStatus(
        isURL(self.requestFormUrl) ? "valid" : "invalid"
      );
      self.setRequestFormTypeStatus(
        !isEmpty(self.requestFormType, { ignore_whitespace: true })
          ? "valid"
          : "invalid"
      );
      self.setRequestFormTrafficStatus(
        !isEmpty(self.requestFormTraffic, { ignore_whitespace: true })
          ? "valid"
          : "invalid"
      );

      return (
        self.requestFormNameStatus === "valid" &&
        self.requestFormEmailStatus === "valid" &&
        self.requestFormUrlStatus === "valid" &&
        self.requestFormTypeStatus === "valid" &&
        self.requestFormTrafficStatus === "valid"
      );
    },
  }));
