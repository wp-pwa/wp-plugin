import React from "react";
import { string } from "prop-types";
import { inject } from "mobx-react";

const App = ({ title }) => <div>{title}</div>;

App.propTypes = {
  title: string.isRequired
};

export default inject(({ stores: { settings } }) => ({
  title: settings.title
}))(App);
