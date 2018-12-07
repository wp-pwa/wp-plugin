import React from "react";
import { string, func } from "prop-types";
import { inject } from "mobx-react";
import { Box, FormField, TextInput, Button } from "grommet";

const Settings = ({ siteId, setSiteId, saveSettings }) => (
  <form onSubmit={saveSettings}>
    <Box width="medium">
      <FormField label="Site ID" htmlFor="site_id">
        <TextInput id="site_id" value={siteId} onChange={setSiteId} />
      </FormField>
      <Button type="submit" label="Save" onClick={saveSettings} primary />
    </Box>
  </form>
);

Settings.propTypes = {
  siteId: string.isRequired,
  setSiteId: func.isRequired,
  saveSettings: func.isRequired
};

export default inject(({ stores: { settings } }) => ({
  siteId: settings.site_id,
  setSiteId: settings.setSiteId,
  saveSettings: settings.saveSettings
}))(Settings);
