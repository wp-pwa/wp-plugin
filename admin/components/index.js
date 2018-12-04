import React from "react";
import { string, func } from "prop-types";
import { inject } from "mobx-react";
import { Grommet, Box, FormField, TextInput, Button } from "grommet";

const App = ({ testSetting, setTestSetting, saveSettings }) => (
  <Grommet plain>
    <form onSubmit={saveSettings}>
      <Box width="medium">
        <FormField label="Test Setting" htmlFor="test-setting">
          <TextInput
            id="test-setting"
            value={testSetting}
            onChange={setTestSetting}
          />
        </FormField>
        <Button type="submit" label="Save" onClick={saveSettings} primary />
      </Box>
    </form>
  </Grommet>
);

App.propTypes = {
  testSetting: string.isRequired,
  setTestSetting: func.isRequired,
  saveSettings: func.isRequired
};

export default inject(({ stores: { settings } }) => ({
  testSetting: settings.test_setting,
  setTestSetting: settings.setTestSetting,
  saveSettings: settings.saveSettings
}))(App);
