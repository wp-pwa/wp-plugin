import React from "react";
import { render } from "react-dom";
import { Provider } from "mobx-react";
import App from "./components";
import Stores from "./models";

const stores = Stores.create({
  settings: window.frontity.plugin.settings
});

window.frontity.client = stores;

render(
  <Provider stores={stores}>
    <App />
  </Provider>,
  window.document.getElementById("root")
);
