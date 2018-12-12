import React from "react";
import { string, bool, func, shape } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import {
  Box,
  Heading,
  Paragraph,
  FormField,
  TextInput,
  CheckBox,
  Button,
  TextArea,
} from "grommet";

const Settings = ({
  siteId,
  ssrServer,
  staticServer,
  ampServer,
  frontpageForced,
  htmlPurifierActive,
  excludes,
  apiFilters,
  setSiteId,
  setSsrServer,
  setStaticServer,
  setAmpServer,
  setFrontpageForced,
  setHtmlPurifierActive,
  setExcludes,
  setApiFilters,
  saveSettings,
  notification,
  formTitleText,
  fieldSiteId,
  fieldSsrServer,
  fieldStaticServer,
  fieldAmpServer,
  fieldForceFrontpage,
  fieldHtmlPurifier,
  fieldExcludes,
  fieldApiFilters,
  saveButtonText,
}) => (
  <Box margin={{ horizontal: "auto", top: "40px" }} width="608px">
    <Notification align="center" margin={{ bottom: "20px" }}>
      <StyledParagraph margin={{ vertical: "0" }}>
        <strong>{notification.highlight} </strong>
        {notification.content}
      </StyledParagraph>
    </Notification>
    <Options margin={{ bottom: "24px" }}>
      <Header margin={{ horizontal: "0", vertical: "0" }}>
        {formTitleText}
      </Header>
      <Form>
        <FormField label={fieldSiteId.label}>
          <TextInput
            placeholder={fieldSiteId.placeholder}
            value={siteId}
            onChange={setSiteId}
          />
        </FormField>
        <FormField label={fieldSsrServer.label}>
          <TextInput
            placeholder={fieldSsrServer.placeholder}
            value={ssrServer}
            onChange={setSsrServer}
          />
        </FormField>
        <FormField label={fieldStaticServer.label}>
          <TextInput
            placeholder={fieldStaticServer.placeholder}
            value={staticServer}
            onChange={setStaticServer}
          />
        </FormField>
        <FormField label={fieldAmpServer.label}>
          <TextInput
            placeholder={fieldAmpServer.placeholder}
            value={ampServer}
            onChange={setAmpServer}
          />
        </FormField>
        <Box
          direction="row"
          justify="between"
          align="start"
          margin={{ bottom: "18px" }}
        >
          <Box direction="row" justify="between" align="center" width="262px">
            <Paragraph margin={{ vertical: "0", left: "12px", right: "20px" }}>
              {fieldForceFrontpage.label}
            </Paragraph>
            <CheckBox
              toggle
              checked={frontpageForced}
              onChange={setFrontpageForced}
            />
          </Box>
          <Comment margin={{ vertical: "0" }}>
            {fieldForceFrontpage.comment}
          </Comment>
        </Box>
        <Box
          direction="row"
          justify="between"
          align="center"
          margin={{ bottom: "18px" }}
        >
          <Box direction="row" justify="between" align="center" width="262px">
            <Paragraph margin={{ vertical: "0", left: "12px", right: "20px" }}>
              {fieldHtmlPurifier.label}
            </Paragraph>
            <CheckBox
              toggle
              checked={htmlPurifierActive}
              onChange={setHtmlPurifierActive}
            />
          </Box>
          <Button label={fieldHtmlPurifier.button} />
        </Box>
        <FormField label={fieldExcludes.label}>
          <TextArea
            placeholder={fieldExcludes.placeholder}
            value={excludes}
            onChange={setExcludes}
          />
        </FormField>
        <FormField label={fieldApiFilters.label}>
          <TextArea
            placeholder={fieldApiFilters.placeholder}
            value={apiFilters}
            onChange={setApiFilters}
          />
        </FormField>
      </Form>
    </Options>
    <Button
      primary
      type="submit"
      label={saveButtonText}
      onClick={saveSettings}
    />
  </Box>
);

Settings.propTypes = {
  siteId: string.isRequired,
  ssrServer: string.isRequired,
  staticServer: string.isRequired,
  ampServer: string.isRequired,
  frontpageForced: bool.isRequired,
  htmlPurifierActive: bool.isRequired,
  excludes: string.isRequired,
  apiFilters: string.isRequired,
  setSiteId: func.isRequired,
  setSsrServer: func.isRequired,
  setStaticServer: func.isRequired,
  setAmpServer: func.isRequired,
  setFrontpageForced: func.isRequired,
  setHtmlPurifierActive: func.isRequired,
  setExcludes: func.isRequired,
  setApiFilters: func.isRequired,
  saveSettings: func.isRequired,
  notification: shape({ highlight: string, content: string }).isRequired,
  formTitleText: string.isRequired,
  fieldSiteId: shape({ label: string, placeholder: string }).isRequired,
  fieldSsrServer: shape({ label: string, placeholder: string }).isRequired,
  fieldStaticServer: shape({ label: string, placeholder: string }).isRequired,
  fieldAmpServer: shape({ label: string, placeholder: string }).isRequired,
  fieldForceFrontpage: shape({ label: string, comment: string }).isRequired,
  fieldHtmlPurifier: shape({ label: string, button: string }).isRequired,
  fieldExcludes: shape({ label: string, placeholder: string }).isRequired,
  fieldApiFilters: shape({ label: string, placeholder: string }).isRequired,
  saveButtonText: string.isRequired,
};

export default inject(({ stores: { settings, languages } }) => {
  const form = "settings.form";

  return {
    siteId: settings.site_id,
    ssrServer: settings.ssr_server,
    staticServer: settings.static_server,
    ampServer: settings.amp_server,
    frontpageForced: settings.frontpage_forced,
    htmlPurifierActive: settings.html_purifier_active,
    excludes: settings.excludes.join("\n"),
    apiFilters: settings.api_filters.join("\n"),
    setSiteId: settings.setSiteId,
    setSsrServer: settings.setSsrServer,
    setStaticServer: settings.setStaticServer,
    setAmpServer: settings.setAmpServer,
    setFrontpageForced: settings.setFrontpageForced,
    setHtmlPurifierActive: settings.setHtmlPurifierActive,
    setExcludes: settings.setExcludes,
    setApiFilters: settings.setApiFilters,
    saveSettings: settings.saveSettings,
    notification: languages.get("settings.notification"),
    formTitleText: languages.get(`${form}.title`),
    fieldSiteId: languages.get(`${form}.fieldSiteId`),
    fieldSsrServer: languages.get(`${form}.fieldSsrServer`),
    fieldStaticServer: languages.get(`${form}.fieldStaticServer`),
    fieldAmpServer: languages.get(`${form}.fieldAmpServer`),
    fieldForceFrontpage: languages.get(`${form}.fieldForceFrontpage`),
    fieldHtmlPurifier: languages.get(`${form}.fieldHtmlPurifier`),
    fieldExcludes: languages.get(`${form}.fieldExcludes`),
    fieldApiFilters: languages.get(`${form}.fieldApiFilters`),
    saveButtonText: languages.get("settings.saveButton"),
  };
})(Settings);

const Notification = styled(Box)`
  border-radius: 4px;
  background-color: #fff;
  padding: 8px;
  box-shadow: 0 1px 4px 0 rgba(31, 56, 197, 0.12);
`;

const Options = styled(Box)`
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
`;

const Form = styled(Box)`
  padding: 32px;
`;

const StyledParagraph = styled(Paragraph)`
  max-width: 100%;
`;

const Comment = styled(Paragraph)`
  width: 250px;
  opacity: 0.4;
  color: #0c112b;
`;
