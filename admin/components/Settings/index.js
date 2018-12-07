import React from "react";
import { string, bool, func } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import {
  Box,
  Heading,
  FormField,
  TextInput,
  CheckBox,
  Button,
  TextArea
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
  saveSettings
}) => (
  <Box margin={{ horizontal: "auto", top: "40px" }} width="608px">
    <Warning
      direction="row"
      align="center"
      height="40px"
      margin={{ bottom: "20px" }}
    >
      Warning: Changing this settings can break your Progressive Web App
    </Warning>
    <Options margin={{ bottom: "24px" }}>
      <Header margin={{ horizontal: "0", vertical: "0" }}>
        Advanced Settings
      </Header>
      <Form>
        <FormField label="Site ID">
          <TextInput
            placeholder="ID of 16 characters"
            value={siteId}
            onChange={setSiteId}
          />
        </FormField>
        <FormField label="SSR Server">
          <TextInput
            placeholder="SSR URL"
            value={ssrServer}
            onChange={setSsrServer}
          />
        </FormField>
        <FormField label="Static Server">
          <TextInput
            placeholder="Static URL"
            value={staticServer}
            onChange={setStaticServer}
          />
        </FormField>
        <FormField label="AMP Server">
          <TextInput
            placeholder="AMP URL"
            value={ampServer}
            onChange={setAmpServer}
          />
        </FormField>
        <FormField label="Force Frontpage">
          <CheckBox
            toggle
            checked={frontpageForced}
            onChange={setFrontpageForced}
          />
        </FormField>
        <FormField label="Activate HTML Purifier">
          <CheckBox
            toggle
            checked={htmlPurifierActive}
            onChange={setHtmlPurifierActive}
          />
        </FormField>
        <Button label="Purge cache" />
        <FormField label="Exclude URLs in the PWA">
          <TextArea
            placeholder={"http://sample.com/page/\nhttp://v2.sample.com/page/"}
            value={excludes}
            onChange={setExcludes}
          />
        </FormField>
        <FormField label="Filter WP API fields">
          <TextArea
            placeholder={"_links\ntitle.rendered"}
            value={apiFilters}
            onChange={setApiFilters}
          />
        </FormField>
      </Form>
    </Options>
    <Button type="submit" label="Save changes" onClick={saveSettings} primary />
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
  saveSettings: func.isRequired
};

export default inject(({ stores: { settings } }) => ({
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
  saveSettings: settings.saveSettings
}))(Settings);

const StyledBox = styled(Box)`
  border-radius: 4px;
  background-color: #fff;
`;

const Warning = styled(StyledBox)`
  padding: 0 8px;
  box-shadow: 0 1px 4px 0 rgba(31, 56, 197, 0.12);
`;

const Options = styled(StyledBox)`
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
