import React from "react";
import { render } from "react-dom";
import { Provider } from "mobx-react";
import { isEmpty } from "validator";
import Admin from "./components";
import Stores from "./models";

import "@babel/polyfill";

const stores = Stores.create({
  general: {
    page: new URLSearchParams(window.location.search).get("page"),
  },
  settings: window.frontity.plugin.settings,
  ui: {
    siteIdStatus: !isEmpty(window.frontity.plugin.settings.site_id, {
      ignore_whitespace: true,
    })
      ? "valid"
      : undefined,
  },
});

window.frontity.client = stores;

render(
  <Provider stores={stores}>
    <Admin />
  </Provider>,
  window.document.getElementById("root")
);
