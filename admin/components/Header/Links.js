import React from "react";
import { Box, Button } from "grommet";

const Links = () => (
  <Box direction="row" align="center" justify="between">
    <Button label="Contant and help" margin={{ right: "20px" }} />
    <Button label="Documentation" margin={{ right: "20px" }} />
    <Button primary label="View demo" />
  </Box>
);

export default Links;
