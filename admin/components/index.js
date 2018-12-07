import React from "react";
import { string, func } from "prop-types";
import { inject } from "mobx-react";
import { Grommet, Box, FormField, TextInput, Button } from "grommet";

const App = ({ siteId, setSiteId, saveSettings }) => (
  <Grommet plain>
    <form onSubmit={saveSettings}>
      <Box width="medium">
        <FormField label="Site ID" htmlFor="site_id">
          <TextInput id="site_id" value={siteId} onChange={setSiteId} />
        </FormField>
        <Button type="submit" label="Save" onClick={saveSettings} primary />
      </Box>
    </form>
  </Grommet>
);

App.propTypes = {
  siteId: string.isRequired,
  setSiteId: func.isRequired,
  saveSettings: func.isRequired
};

export default inject(({ stores: { settings } }) => ({
  siteId: settings.site_id,
  setSiteId: settings.setSiteId,
  saveSettings: settings.saveSettings
}))(App);
