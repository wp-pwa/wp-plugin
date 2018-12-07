import React from "react";
import { Box, Button } from "grommet";

const Links = () => (
  <Box direction="row" align="center" justify="between">
    <Button
      label="Contant and help"
      focusIndicator={false}
      margin={{ right: "20px" }}
    />
    <Button
      label="Documentation"
      focusIndicator={false}
      margin={{ right: "20px" }}
    />
    <Button primary label="View demo" focusIndicator={false} />
  </Box>
);

export default Links;
