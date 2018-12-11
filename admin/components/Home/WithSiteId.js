import React from "react";
import { bool, func } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import { Box, Heading, Paragraph, CheckBox } from "grommet";

const WithSiteId = ({ pwaActive, ampActive, setPwaActive, setAmpActive }) => (
  <>
    <Notification margin={{ top: "40px", bottom: "20px" }}>
      You’re all set! Now check your PWA configuration.
    </Notification>
    <Container margin={{ bottom: "20px" }}>
      <Box direction="row" justify="between">
        <StyledHeading margin={{ vertical: "0", horizontal: "0" }}>
          Progressive Web App Theme
        </StyledHeading>
        <CheckBox toggle checked={pwaActive} onChange={setPwaActive} />
      </Box>
      <Comment>
        Activate this option to replace your current mobile version with our
        Progressive Web App theme.
      </Comment>
    </Container>
    <Container margin={{ bottom: "40px" }}>
      <Box direction="row" justify="between">
        <StyledHeading margin={{ vertical: "0", horizontal: "0" }}>
          Google AMP
        </StyledHeading>
        <CheckBox toggle checked={ampActive} onChange={setAmpActive} />
      </Box>
      <Comment>
        Activate Google AMP on your mobile site with the same look and feel of
        the PWA theme.
      </Comment>
    </Container>
    <Separator />
    <Notification>
      If you like Frontity and appreciate our work, please leave a positive
      review to support continued development.
    </Notification>
  </>
);

WithSiteId.propTypes = {
  pwaActive: bool.isRequired,
  ampActive: bool.isRequired,
  setPwaActive: func.isRequired,
  setAmpActive: func.isRequired
};

export default inject(({ stores: { settings } }) => ({
  pwaActive: settings.pwa_active,
  ampActive: settings.amp_active,
  setPwaActive: settings.setPwaActive,
  setAmpActive: settings.setAmpActive
}))(WithSiteId);

const StyledBox = styled(Box)`
  border-radius: 4px;
  background-color: #fff;
`;

const Container = styled(StyledBox)`
  box-shadow: 0 1px 4px 0 rgba(31, 56, 197, 0.12),
    0 8px 12px 0 rgba(31, 56, 197, 0.12);
  padding: 32px 32px 24px 32px;
`;

const Notification = styled(StyledBox)`
  padding: 8px;
  box-shadow: 0 1px 4px 0 rgba(31, 56, 197, 0.12);
`;

const StyledParagraph = styled(Paragraph)`
  max-width: 100%;
`;

const StyledHeading = styled(Heading)`
  font-size: 24px;
  font-weight: 600;
  line-height: 1.33;
  color: #24282e;
`;

const Comment = styled(StyledParagraph)`
  color: #0c112b;
  opacity: 0.4;´
`;

const Separator = styled.div`
  width: 608px;
  height: 2px;
  opacity: 0.08;
  background-color: #1f38c5;
  margin-bottom: 40px;
`;
