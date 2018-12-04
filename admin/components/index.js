import React from "react";
import { string } from "prop-types";
import { inject } from "mobx-react";
import { Grommet } from "grommet";

const App = ({ title }) => <Grommet plain>{title}</Grommet>;

App.propTypes = {
  title: string.isRequired
};

export default inject(({ stores: { settings } }) => ({
  title: settings.title
}))(App);
