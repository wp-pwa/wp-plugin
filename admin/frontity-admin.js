import React from "react";
import { render } from "react-dom";
import { Provider } from "mobx-react";
import App from "./components";
import Main from "./models";

const stores = Main.create();

render(
  <Provider stores={stores}>
    <App />
  </Provider>,
  window.document.getElementById("root")
);
