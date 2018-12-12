import React from "react";
import { string } from "prop-types";
import { inject } from "mobx-react";
import { Grommet } from "grommet";
import Styles from "./Styles";
import Header from "./Header";
import Home from "./Home";
import Settings from "./Settings";

const pages = {
  "frontity-dashboard": <Home />,
  "frontity-settings": <Settings />,
};

const Admin = ({ page }) => (
  <Grommet
    theme={{
      global: {
        colors: {
          brand: "#1F38C5",
        },
      },
    }}
  >
    <Styles />
    <Header />
    {pages[page]}
  </Grommet>
);

Admin.propTypes = {
  page: string.isRequired,
};

export default inject(({ stores: { general } }) => ({
  page: general.page,
}))(Admin);
