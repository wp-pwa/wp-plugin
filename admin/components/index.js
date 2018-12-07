import React from "react";
import { string } from "prop-types";
import { inject } from "mobx-react";
import { Grommet } from "grommet";
import Home from "./Home";
import Settings from "./Settings";

const pages = {
  frontity_home: <Home />,
  frontity_settings: <Settings />
};

const Admin = ({ adminPage }) => <Grommet plain>{pages[adminPage]}</Grommet>;

Admin.propTypes = {
  adminPage: string.isRequired
};

export default inject(({ stores: { general } }) => ({
  adminPage: general.admin_page
}))(Admin);
