/* eslint-disable jsx-a11y/anchor-is-valid */
import React from "react";
import { string, bool, func, shape } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import { Box, Heading, Paragraph, CheckBox } from "grommet";

const WithSiteId = ({
  pwaActive,
  ampActive,
  setPwaActive,
  setAmpActive,
  topNotificationText,
  bottomNotification,
  pwaTitleText,
  pwaContentText,
  ampTitleText,
  ampContentText,
  linkTwitterText,
  linkGithubText,
}) => (
  <>
    <Notification margin={{ top: "40px", bottom: "20px" }}>
      {topNotificationText}
    </Notification>
    <Container margin={{ bottom: "20px" }}>
      <Box direction="row" justify="between">
        <StyledHeading margin={{ vertical: "0", horizontal: "0" }}>
          {pwaTitleText}
        </StyledHeading>
        <CheckBox toggle checked={pwaActive} onChange={setPwaActive} />
      </Box>
      <Comment>{pwaContentText}</Comment>
    </Container>
    <Container margin={{ bottom: "40px" }}>
      <Box direction="row" justify="between">
        <StyledHeading margin={{ vertical: "0", horizontal: "0" }}>
          {ampTitleText}
        </StyledHeading>
        <CheckBox toggle checked={ampActive} onChange={setAmpActive} />
      </Box>
      <Comment>{ampContentText}</Comment>
    </Container>
    <Separator />
    <Notification>
      <StyledParagraph margin={{ vertical: "0" }}>
        {bottomNotification.contentPreLink}
        <NotificationLink
          href="https://wordpress.org/support/plugin/wp-pwa/reviews/?filter=5"
          target="_blank"
        >
          {" "}
          {bottomNotification.link}{" "}
        </NotificationLink>
        {bottomNotification.contentPostLink}
      </StyledParagraph>
    </Notification>
    <Box direction="row" justify="between" margin={{ top: "16px" }}>
      <Link href="https://twitter.com/frontity" target="_blank">
        {linkTwitterText}
      </Link>
      <Link href="https://github.com/frontity" target="_blank">
        {linkGithubText}
      </Link>
    </Box>
  </>
);

WithSiteId.propTypes = {
  pwaActive: bool.isRequired,
  ampActive: bool.isRequired,
  setPwaActive: func.isRequired,
  setAmpActive: func.isRequired,
  topNotificationText: string.isRequired,
  bottomNotification: shape({
    contentPreLink: string,
    link: string,
    contentPostLink: string,
  }).isRequired,
  pwaTitleText: string.isRequired,
  pwaContentText: string.isRequired,
  ampTitleText: string.isRequired,
  ampContentText: string.isRequired,
  linkTwitterText: string.isRequired,
  linkGithubText: string.isRequired,
};

export default inject(({ stores: { settings, languages } }) => {
  const withSiteId = "home.withSiteId";
  const notifications = `${withSiteId}.notifications`;
  const links = `${withSiteId}.links`;

  return {
    pwaActive: settings.pwa_active,
    ampActive: settings.amp_active,
    setPwaActive: settings.setPwaActive,
    setAmpActive: settings.setAmpActive,
    topNotificationText: languages.get(`${notifications}.top`),
    bottomNotification: languages.get(`${notifications}.bottom`),
    pwaTitleText: languages.get(`${withSiteId}.pwaActivation.title`),
    pwaContentText: languages.get(`${withSiteId}.pwaActivation.content`),
    ampTitleText: languages.get(`${withSiteId}.ampActivation.title`),
    ampContentText: languages.get(`${withSiteId}.ampActivation.content`),
    linkTwitterText: languages.get(`${links}.twitter`),
    linkGithubText: languages.get(`${links}.github`),
  };
})(WithSiteId);

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
  height: 2px;
  opacity: 0.08;
  background-color: #1f38c5;
  margin-bottom: 40px;
`;

const Link = styled.a`
  color: #1f38c5;
  text-decoration: underline;
`;

const NotificationLink = styled.a`
  color: #1f38c5;
  text-decoration: none;
`;
