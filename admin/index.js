import React from "react";
import { render } from "react-dom";
import { Provider } from "mobx-react";
import Admin from "./components";
import Stores from "./models";

import "@babel/polyfill";

const stores = Stores.create({
  general: {
    pluginDirUrl: window.frontity.plugin.plugin_dir_url,
    page: new URLSearchParams(window.location.search).get("page"),
  },
  settings: window.frontity.plugin.settings,
  validations: {
    settings: {
      site_id: window.frontity.plugin.settings.site_id ? "valid" : undefined,
    },
  },
  languages: {
    code: window.frontity.plugin.locale,
  },
});

window.frontity.client = stores;

render(
  <Provider stores={stores}>
    <Admin />
  </Provider>,
  window.document.getElementById("root")
);
