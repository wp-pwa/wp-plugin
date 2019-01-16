import React from "react";
import { string } from "prop-types";
import { inject } from "mobx-react";
import { Box } from "grommet";
import WithSiteId from "./WithSiteId";
import WithoutSiteId from "./WithoutSiteId";

const Home = ({ siteIdValidation }) => (
  <Box margin="auto" width="632px" pad={{ horizontal: "12px" }}>
    {siteIdValidation === "valid" ? <WithSiteId /> : <WithoutSiteId />}
  </Box>
);

Home.propTypes = {
  siteIdValidation: string,
};

Home.defaultProps = {
  siteIdValidation: undefined,
};

export default inject(({ stores: { validations } }) => ({
  siteIdValidation: validations.settings.site_id,
}))(Home);
