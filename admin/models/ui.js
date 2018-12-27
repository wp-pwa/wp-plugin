import { types, getParent } from "mobx-state-tree";
import { isEmpty, isURL, isEmail } from "validator";
import { post } from "axios";

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
        !isEmpty(settings.site_id, { ignore_whitespace: true })
          ? "valid"
          : "invalid"
      );
      self.setSsrServerStatus(
        isURL(settings.ssr_server, { require_tld: false }) ? "valid" : "invalid"
      );
      self.setStaticServerStatus(
        isURL(settings.static_server, { require_tld: false })
          ? "valid"
          : "invalid"
      );
      self.setAmpServerStatus(
        isURL(settings.amp_server, { require_tld: false }) ? "valid" : "invalid"
      );

      return (
        self.siteIdStatus === "valid" &&
        self.ssrServerStatus === "valid" &&
        self.staticServerStatus === "valid" &&
        self.ampServerStatus === "valid"
      );
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
    async sendRequest() {
      const siteTypes = {
        "Blog / News Site": "blog",
        "eCommerce / Online store": "ecommerce",
        "Corporate site / Online bussiness": "corpsite",
        "Classifieds site": "classifiedsite",
        Other: "other",
      };

      const siteTraffics = {
        "More than 1 million": "A",
        "500.000 - 1 million": "B",
        "100.000 - 500.000": "C",
        "Less than 100.000": "D",
        "I don't know": "Unknown",
      };

      await post(
        "https://hook.integromat.com/214srcvxlj88frdnqaua6vipqvsnmjgo",
        {
          name: self.requestFormName,
          email: self.requestFormEmail,
          url: self.requestFormUrl,
          type: siteTypes[self.requestFormType],
          traffic: siteTraffics[self.requestFormTraffic],
          source: "plugin",
        }
      );
    },
  }));
