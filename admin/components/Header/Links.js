import React from "react";
import { string } from "prop-types";
import { inject } from "mobx-react";
import { Box, Button } from "grommet";

const Links = ({ contactAndHelpText, documentationText, viewDemoText }) => (
  <Box direction="row" align="center" justify="between">
    <Button
      label={contactAndHelpText}
      focusIndicator={false}
      margin={{ right: "20px" }}
    />
    <Button
      label={documentationText}
      focusIndicator={false}
      margin={{ right: "20px" }}
    />
    <Button primary label={viewDemoText} focusIndicator={false} />
  </Box>
);

Links.propTypes = {
  contactAndHelpText: string.isRequired,
  documentationText: string.isRequired,
  viewDemoText: string.isRequired,
};

export default inject(({ stores: { languages } }) => ({
  contactAndHelpText: languages.get("header.links.contactAndHelp"),
  documentationText: languages.get("header.links.documentation"),
  viewDemoText: languages.get("header.links.viewDemo"),
}))(Links);
