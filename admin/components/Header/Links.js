/* eslint-disable react/jsx-no-target-blank */
import React from "react";
import { string, func } from "prop-types";
import { inject } from "mobx-react";
import { Box, Button } from "grommet";

const Links = ({
  contactAndHelpText,
  documentationText,
  viewDemoText,
  openContactAndHelp,
  openDocumentation,
}) => (
  <Box direction="row" align="center" justify="between">
    <Button
      label={contactAndHelpText}
      focusIndicator={false}
      margin={{ right: "20px" }}
      onClick={openContactAndHelp}
    />
    <Button
      label={documentationText}
      focusIndicator={false}
      margin={{ right: "20px" }}
      onClick={openDocumentation}
    />
    <Button
      primary
      label={viewDemoText}
      focusIndicator={false}
      href="https://frontity.com/demo"
      target="_blank"
    />
  </Box>
);

Links.propTypes = {
  contactAndHelpText: string.isRequired,
  documentationText: string.isRequired,
  viewDemoText: string.isRequired,
  openContactAndHelp: func.isRequired,
  openDocumentation: func.isRequired,
};

export default inject(({ stores: { languages } }) => ({
  contactAndHelpText: languages.get("header.links.contactAndHelp"),
  documentationText: languages.get("header.links.documentation"),
  viewDemoText: languages.get("header.links.viewDemo"),
  openContactAndHelp: () =>
    window.open(
      "https://frontity.com/get-help?utm_source=plugin-dashboard&utm_medium=cta-button&utm_campaign=plugin-dashboard",
      "_blank"
    ),
  openDocumentation: () =>
    window.open("https://support.frontity.com/", "_blank"),
}))(Links);
