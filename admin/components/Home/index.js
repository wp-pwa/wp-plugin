import React from "react";
import { string } from "prop-types";
import { inject } from "mobx-react";
import { Box } from "grommet";
import WithSiteId from "./WithSiteId";
import WithoutSiteId from "./WithoutSiteId";

const Home = ({ siteIdStatus }) => (
  <Box margin="auto" width="608px">
    {siteIdStatus === "valid" ? <WithSiteId /> : <WithoutSiteId />}
  </Box>
);

Home.propTypes = {
  siteIdStatus: string,
};

Home.defaultProps = {
  siteIdStatus: undefined,
};

export default inject(({ stores: { ui } }) => ({
  siteIdStatus: ui.siteIdStatus,
}))(Home);
