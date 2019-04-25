/* eslint-disable react/jsx-no-target-blank */
import React from "react";
import { string, func } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import { Box, Button } from "grommet";

const Links = ({ documentationText, openDocumentation }) => (
  <StyledBox direction="row" align="center" justify="between">
    <Button
      label={documentationText}
      focusIndicator={false}
      margin={{ right: "20px" }}
      onClick={openDocumentation}
    />
  </StyledBox>
);

Links.propTypes = {
  documentationText: string.isRequired,
  openDocumentation: func.isRequired,
};

export default inject(({ stores: { languages } }) => ({
  documentationText: languages.get("header.links.documentation"),
  openDocumentation: () =>
    window.open("https://support.frontity.com/", "_blank"),
}))(Links);

const StyledBox = styled(Box)`
  @media (max-width: 582px) {
    align-items: center;
    flex-direction: column;
    & > * {
      margin: 0;
    }

    & > button:nth-of-type(2) {
      margin: 8px 0;
    }
  }
`;
