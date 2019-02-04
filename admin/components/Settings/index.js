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
  injectionType,
  setSiteId,
  setSsrServer,
  setStaticServer,
  setAmpServer,
  setFrontpageForced,
  setHtmlPurifierActive,
  setExcludes,
  setInjectionType,
  saveSettings,
  purgeHtmlPurifierCache,
  notification,
  formTitleText,
  fieldSiteId,
  fieldSsrServer,
  fieldStaticServer,
  fieldAmpServer,
  fieldForceFrontpage,
  fieldHtmlPurifier,
  fieldExcludes,
  fieldInjectionType,
  saveButtonText,
  siteIdValidation,
  ssrServerValidation,
  staticServerValidation,
  ampServerValidation,
  saveButtonStatus,
  purgePurifierButtonStatus,
}) => (
  <Box
    margin={{ horizontal: "auto", top: "40px" }}
    width="632px"
    pad={{ horizontal: "12px" }}
  >
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
        <form id="settings-form" onSubmit={saveSettings}>
          <FormField label={fieldSiteId.label}>
            <StyledTextInput
              validation={siteIdValidation}
              placeholder={fieldSiteId.placeholder}
              value={siteId}
              onChange={setSiteId}
            />
          </FormField>
          <FormField label={fieldSsrServer.label}>
            <StyledTextInput
              validation={ssrServerValidation}
              placeholder={fieldSsrServer.placeholder}
              value={ssrServer}
              onChange={setSsrServer}
            />
          </FormField>
          <FormField label={fieldStaticServer.label}>
            <StyledTextInput
              validation={staticServerValidation}
              placeholder={fieldStaticServer.placeholder}
              value={staticServer}
              onChange={setStaticServer}
            />
          </FormField>
          <FormField label={fieldAmpServer.label}>
            <StyledTextInput
              validation={ampServerValidation}
              placeholder={fieldAmpServer.placeholder}
              value={ampServer}
              onChange={setAmpServer}
            />
          </FormField>
          <StyledBox
            direction="row"
            justify="between"
            align="start"
            margin={{ bottom: "18px" }}
          >
            <Box direction="row" justify="between" align="center" width="262px">
              <Paragraph
                margin={{ vertical: "0", left: "12px", right: "20px" }}
              >
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
          </StyledBox>
          <StyledBox
            direction="row"
            justify="between"
            align="center"
            margin={{ bottom: "18px" }}
          >
            <Box direction="row" justify="between" align="center" width="262px">
              <Paragraph
                margin={{ vertical: "0", left: "12px", right: "20px" }}
              >
                {fieldHtmlPurifier.label}
              </Paragraph>
              <CheckBox
                toggle
                checked={htmlPurifierActive}
                onChange={setHtmlPurifierActive}
              />
            </Box>
            <PurgeButton
              disabled={purgePurifierButtonStatus !== "idle"}
              label={fieldHtmlPurifier.button[purgePurifierButtonStatus]}
              onClick={purgeHtmlPurifierCache}
            />
          </StyledBox>
          <FormField label={fieldExcludes.label}>
            <TextArea
              placeholder={fieldExcludes.placeholder}
              value={excludes}
              onChange={setExcludes}
            />
          </FormField>
          <Box direction="row" justify="between" align="center" width="262px">
            <Paragraph margin={{ vertical: "0", left: "12px", right: "20px" }}>
              {fieldInjectionType.label}
            </Paragraph>
            <select value={injectionType} onChange={setInjectionType}>
              {Object.entries(fieldInjectionType.options).map(
                ([option, label]) => (
                  <option key={option} value={option}>
                    {label}
                  </option>
                )
              )}
            </select>
          </Box>
        </form>
      </Form>
    </Options>
    <SaveButton
      form="settings-form"
      primary
      disabled={saveButtonStatus !== "idle"}
      margin={{ left: "auto" }}
      type="submit"
      label={saveButtonText[saveButtonStatus]}
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
  injectionType: string.isRequired,
  setSiteId: func.isRequired,
  setSsrServer: func.isRequired,
  setStaticServer: func.isRequired,
  setAmpServer: func.isRequired,
  setFrontpageForced: func.isRequired,
  setHtmlPurifierActive: func.isRequired,
  setExcludes: func.isRequired,
  setInjectionType: func.isRequired,
  saveSettings: func.isRequired,
  purgeHtmlPurifierCache: func.isRequired,
  notification: shape({ highlight: string, content: string }).isRequired,
  formTitleText: string.isRequired,
  fieldSiteId: shape({ label: string, placeholder: string }).isRequired,
  fieldSsrServer: shape({ label: string, placeholder: string }).isRequired,
  fieldStaticServer: shape({ label: string, placeholder: string }).isRequired,
  fieldAmpServer: shape({ label: string, placeholder: string }).isRequired,
  fieldForceFrontpage: shape({ label: string, comment: string }).isRequired,
  fieldHtmlPurifier: shape({
    label: string,
    button: shape({ idle: string, busy: string, done: string }),
  }).isRequired,
  fieldExcludes: shape({ label: string, placeholder: string }).isRequired,
  fieldInjectionType: shape({
    label: string,
    options: shape({ inline: string, external: string }),
  }).isRequired,
  saveButtonText: shape({ idle: string, busy: string, done: string })
    .isRequired,
  siteIdValidation: string,
  ssrServerValidation: string,
  staticServerValidation: string,
  ampServerValidation: string,
  saveButtonStatus: string.isRequired,
  purgePurifierButtonStatus: string.isRequired,
};

Settings.defaultProps = {
  siteIdValidation: undefined,
  ssrServerValidation: undefined,
  staticServerValidation: undefined,
  ampServerValidation: undefined,
};

export default inject(
  ({ stores: { general, validations, settings, languages } }) => {
    const form = "settings.form";

    return {
      siteId: settings.site_id,
      ssrServer: settings.ssr_server,
      staticServer: settings.static_server,
      ampServer: settings.amp_server,
      frontpageForced: settings.frontpage_forced,
      htmlPurifierActive: settings.html_purifier_active,
      excludes: settings.excludes.join("\n"),
      injectionType: settings.injection_type,
      setSiteId: settings.setSiteId,
      setSsrServer: settings.setSsrServer,
      setStaticServer: settings.setStaticServer,
      setAmpServer: settings.setAmpServer,
      setFrontpageForced: settings.setFrontpageForced,
      setHtmlPurifierActive: settings.setHtmlPurifierActive,
      setExcludes: settings.setExcludes,
      setInjectionType: settings.setInjectionType,
      saveSettings: settings.saveSettings,
      purgeHtmlPurifierCache: settings.purgeHtmlPurifierCache,
      siteIdValidation: validations.settings.site_id,
      ssrServerValidation: validations.settings.ssr_server,
      staticServerValidation: validations.settings.static_server,
      ampServerValidation: validations.settings.amp_server,
      saveButtonStatus: general.saveButtonStatus,
      purgePurifierButtonStatus: general.purgePurifierButtonStatus,
      notification: languages.get("settings.notification"),
      formTitleText: languages.get(`${form}.title`),
      fieldSiteId: languages.get(`${form}.fieldSiteId`),
      fieldSsrServer: languages.get(`${form}.fieldSsrServer`),
      fieldStaticServer: languages.get(`${form}.fieldStaticServer`),
      fieldAmpServer: languages.get(`${form}.fieldAmpServer`),
      fieldForceFrontpage: languages.get(`${form}.fieldForceFrontpage`),
      fieldHtmlPurifier: languages.get(`${form}.fieldHtmlPurifier`),
      fieldExcludes: languages.get(`${form}.fieldExcludes`),
      fieldInjectionType: languages.get(`${form}.fieldInjectionType`),
      saveButtonText: languages.get("settings.saveButton"),
    };
  }
)(Settings);

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

const StyledTextInput = styled(TextInput)`
  ${({ validation }) =>
    validation === "invalid" ? "background-color: #ea5a3555;" : ""}
`;

const SaveButton = styled(Button)`
  width: 162px;
`;
const PurgeButton = styled(Button)`
  width: 197px;
`;

const StyledBox = styled(Box)`
  @media (max-width: 782px) {
    flex-direction: column;
    align-items: flex-start;

    & > p,
    & > button {
      margin-left: 12px;
      margin-top: 8px;
    }
  }
`;
