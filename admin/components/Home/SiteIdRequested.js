/* eslint-disable jsx-a11y/anchor-is-valid */
import React from "react";
import { string, func, shape } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import { Box, Heading, Paragraph, FormField, TextInput, Button } from "grommet";

const SiteIdRequested = ({
  siteId,
  setSiteId,
  saveSettings,
  setSiteIdRequested,
  titleText,
  contentText,
  fieldSiteId,
  linkText,
  confirmButtonText,
  siteIdValidation,
}) => (
  <form onSubmit={saveSettings}>
    <Container margin={{ top: "40px", bottom: "24px" }}>
      <Header margin={{ horizontal: "0", vertical: "0" }}>{titleText}</Header>
      <Body>
        <Comment>{contentText}</Comment>
        <FormField label={fieldSiteId.label}>
          <StyledTextInput
            status={siteIdValidation}
            placeholder={fieldSiteId.placeholder}
            value={siteId}
            onChange={setSiteId}
          />
        </FormField>
      </Body>
    </Container>
    <Box direction="row" justify="between" align="center">
      <Link onClick={setSiteIdRequested}>{linkText}</Link>
      <Button primary label={confirmButtonText} type="submit" />
    </Box>
  </form>
);

SiteIdRequested.propTypes = {
  siteId: string.isRequired,
  setSiteId: func.isRequired,
  saveSettings: func.isRequired,
  setSiteIdRequested: func.isRequired,
  titleText: string.isRequired,
  contentText: string.isRequired,
  fieldSiteId: shape({ label: string, placeholder: string }).isRequired,
  linkText: string.isRequired,
  confirmButtonText: string.isRequired,
  siteIdValidation: string,
};

SiteIdRequested.defaultProps = {
  siteIdValidation: undefined,
};

export default inject(({ stores: { settings, validations, languages } }) => {
  const siteIdRequested = "home.siteIdRequested";

  return {
    siteId: settings.site_id,
    setSiteId: settings.setSiteId,
    saveSettings: settings.saveSettings,
    setSiteIdRequested: () => settings.setSiteIdRequested(false),
    titleText: languages.get(`${siteIdRequested}.title`),
    contentText: languages.get(`${siteIdRequested}.content`),
    fieldSiteId: languages.get(`${siteIdRequested}.fieldSiteId`),
    linkText: languages.get(`${siteIdRequested}.link`),
    confirmButtonText: languages.get(`${siteIdRequested}.confirmButton`),
    siteIdValidation: validations.settings.site_id,
  };
})(SiteIdRequested);

const Container = styled(Box)`
  border-radius: 4px;
  background-color: #fff;
  box-shadow: 0 1px 4px 0 rgba(31, 56, 197, 0.12),
    0 8px 12px 0 rgba(31, 56, 197, 0.12);
`;

const Header = styled(Heading)`
  display: block;
  background-color: #f6f9fa;
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  padding: 32px;
  font-size: 24px;
  font-weight: 600;

  & > span {
    margin-right: 5px;
  }
`;

const Body = styled(Box)`
  padding: 20px 32px 32px 32px;
`;

const Comment = styled(Paragraph)`
  max-width: 100%;
  color: #0c112b;
  opacity: 0.4;
`;

const Link = styled.a`
  color: #1f38c5;
  text-decoration: underline;
`;

const StyledTextInput = styled(TextInput)`
  ${({ status }) =>
    status === "invalid" ? "background-color: #ea5a3555;" : ""}
`;
