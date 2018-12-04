import { types, getSnapshot } from "mobx-state-tree";
import { post } from "axios";

export default types
  .model("Settings", {
    test_setting: "manolo"
  })
  .actions(self => ({
    setTestSetting({ target }) {
      self.test_setting = target.value;
    },
    async saveSettings(event) {
      event.preventDefault();

      const data = new window.FormData();
      data.append("action", "frontity_save_settings");
      data.append("data", JSON.stringify(getSnapshot(self)));

      await post(window.ajaxurl, data);
    }
  }));
