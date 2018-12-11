import React from "react";
import { bool } from "prop-types";
import { inject } from "mobx-react";
import { Box } from "grommet";
import WithSiteId from "./WithSiteId";
import WithoutSiteId from "./WithoutSiteId";

const Home = ({ siteIdValid }) => (
  <Box margin="auto" width="608px">
    {siteIdValid ? <WithSiteId /> : <WithoutSiteId />}
  </Box>
);

Home.propTypes = {
  siteIdValid: bool.isRequired
};

export default inject(({ stores: { ui } }) => ({
  siteIdValid: ui.siteIdValid
}))(Home);
