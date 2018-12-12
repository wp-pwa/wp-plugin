import React from "react";
import { string, func, shape } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import { Box, Heading, Paragraph, FormField, TextInput, Button } from "grommet";

const SiteIdRequested = ({
  siteId,
  setSiteId,
  saveSettings,
  titleText,
  contentText,
  fieldSiteId,
  confirmButtonText,
}) => (
  <>
    <Container margin={{ top: "40px", bottom: "24px" }}>
      <Header margin={{ horizontal: "0", vertical: "0" }}>{titleText}</Header>
      <Body>
        <Comment>{contentText}</Comment>
        <FormField label={fieldSiteId.label}>
          <TextInput
            placeholder={fieldSiteId.placeholder}
            value={siteId}
            onChange={setSiteId}
          />
        </FormField>
      </Body>
    </Container>
    <Button primary label={confirmButtonText} onClick={saveSettings} />
  </>
);

SiteIdRequested.propTypes = {
  siteId: string.isRequired,
  setSiteId: func.isRequired,
  saveSettings: func.isRequired,
  titleText: string.isRequired,
  contentText: string.isRequired,
  fieldSiteId: shape({ label: string, placeholder: string }).isRequired,
  confirmButtonText: string.isRequired,
};

export default inject(({ stores: { settings, languages } }) => {
  const siteIdRequested = "home.siteIdRequested";

  return {
    siteId: settings.site_id,
    setSiteId: settings.setSiteId,
    saveSettings: settings.saveSettings,
    titleText: languages.get(`${siteIdRequested}.title`),
    contentText: languages.get(`${siteIdRequested}.content`),
    fieldSiteId: languages.get(`${siteIdRequested}.fieldSiteId`),
    confirmButtonText: languages.get(`${siteIdRequested}.confirmButton`),
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
  line-height: 100px;
  background-color: #f6f9fa;
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  padding: 0 32px;
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
