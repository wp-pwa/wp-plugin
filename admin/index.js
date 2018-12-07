import React from "react";
import { render } from "react-dom";
import { Provider } from "mobx-react";
import Admin from "./components";
import Stores from "./models";

import "@babel/polyfill";

const stores = Stores.create({
  general: {
    page: new URLSearchParams(window.location.search).get("page")
  },
  settings: window.frontity.plugin.settings
});

window.frontity.client = stores;

render(
  <Provider stores={stores}>
    <Admin />
  </Provider>,
  window.document.getElementById("root")
);
