import { types, getParent } from "mobx-state-tree";
import { post } from "axios";

export default types
  .model("Request")
  .props({
    name: "",
    email: "",
    url: "",
    type: "",
    traffic: "",
  })
  .views(self => ({
    get root() {
      return getParent(self, 1);
    },
    get validations() {
      return self.root.validations.request;
    },
  }))
  .actions(self => ({
    setName({ target }) {
      self.name = target.value;
      if (self.validations.name) self.validations.clear("name");
    },
    setEmail({ target }) {
      self.email = target.value;
      if (self.validations.email) self.validations.clear("email");
    },
    setUrl({ target }) {
      self.url = target.value;
      if (self.validations.url) self.validations.clear("url");
    },
    setType({ target }) {
      self.type = target.name;
      if (self.validations.type) self.validations.clear("type");
    },
    setTraffic({ target }) {
      self.traffic = target.name;
      if (self.validations.traffic) self.validations.clear("traffic");
    },
    async sendRequest() {
      const typeOptions = {
        "Blog / News Site": "blog",
        "eCommerce / Online store": "ecommerce",
        "Corporate site / Online bussiness": "corpsite",
        "Classifieds site": "classifiedsite",
        Other: "other",
      };

      const trafficOptions = {
        "More than 1 million": "A",
        "500.000 - 1 million": "B",
        "100.000 - 500.000": "C",
        "Less than 100.000": "D",
        "I don't know": "UNKNOWN",
      };

      self.name = self.name.trim();
      self.email = self.email.trim();
      self.url = self.url.trim();

      if (self.validate()) {
        await post(
          "https://hook.integromat.com/214srcvxlj88frdnqaua6vipqvsnmjgo",
          {
            name: self.name,
            email: self.email,
            url: self.url,
            type: typeOptions[self.type],
            traffic: trafficOptions[self.traffic],
            origin: "plugin",
          }
        );

        self.root.general.setSiteIdJustRequested(true);
        self.root.settings.setSiteIdRequested(true);
      }
    },
    validate() {
      return self.validations.validate();
    },
  }));
