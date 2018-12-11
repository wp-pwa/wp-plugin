/* eslint-disable jsx-a11y/anchor-is-valid */
import React from "react";
import { bool, func } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import { Box, Heading, Paragraph, FormField, TextInput, Button } from "grommet";
import RequestForm from "./RequestForm";

const WithoutSiteId = ({
  siteId,
  setSiteId,
  siteIdRequested,
  siteIdJustRequested,
  setSiteIdJustRequested,
  saveSettings
}) => {
  if (siteIdRequested) {
    return siteIdJustRequested ? (
      <Container margin={{ top: "40px" }}>
        <Header margin={{ horizontal: "0", vertical: "0" }}>
          <span aria-label="Party emoji" role="img">
            🎉
          </span>
          We have received your request, thanks!
        </Header>
        <Body>
          <StyledParagraph margin={{ vertical: "0", horizontal: "0" }}>
            Our team will manually review your request and get back to you
            shortly.
          </StyledParagraph>
        </Body>
      </Container>
    ) : (
      <>
        <Container margin={{ top: "40px", bottom: "24px" }}>
          <Header margin={{ horizontal: "0", vertical: "0" }}>
            Insert your Site ID
          </Header>
          <Body>
            <Comment>
              The Site ID connects your WordPress website with our platform.
            </Comment>
            <FormField label="Site ID">
              <TextInput
                placeholder="ID of 17 characters"
                value={siteId}
                onChange={setSiteId}
              />
            </FormField>
          </Body>
        </Container>
        <Button label="Confirm" primary onClick={saveSettings} />
      </>
    );
  }

  return (
    <>
      <OuterContainer>
        <InnerContainer>
          <Image
            alt="Frontity Theme"
            src="/wp-content/plugins/frontity-plugin/admin/assets/frontity-theme.png"
          />
          <Box>Image footer</Box>
        </InnerContainer>
        <InnerContainer>
          <Heading size="small" margin={{ top: "0", bottom: "16px" }}>
            How Frontity works
          </Heading>
          <Paragraph margin={{ top: "0", bottom: "24px" }}>
            Frontity is a mobile WordPress theme built with React that makes
            your site faster and more engaging on mobile devices. It implements
            Progressive Web App (PWA) functionalities and Google AMP.{" "}
          </Paragraph>
          <Box>
            {[
              {
                title: "Same URL",
                content:
                  "Frontity uses the same URL as your blog. When a visit comes from mobile, it automatically loads our theme."
              },
              {
                title: "Keep Your Desktop Theme",
                content:
                  "Frontity is a special theme for mobile visitors, which means that your desktop version and content don't change."
              },
              {
                title: "Include / Exclude Pages",
                content:
                  "You can control your menu, pages, and even exclude specific URLs from being loaded into our mobile theme."
              }
            ].map(item => (
              <Box key={item.title}>
                <Heading level={4} margin={{ top: "0", bottom: "4px" }}>
                  {item.title}
                </Heading>
                <Paragraph margin={{ top: "0", bottom: "12px" }}>
                  {item.content}
                </Paragraph>
              </Box>
            ))}
          </Box>
          <ViewDemoButton label="View demo" />
        </InnerContainer>
      </OuterContainer>
      <Separator />
      <Notification margin={{ bottom: "8px" }}>
        <StyledParagraph margin={{ vertical: "0", horizontal: "0" }}>
          <strong>
            Our theme is suitable for news publishers and bloggers
          </strong>{" "}
          (blogs, news sites, magazines) who wish to improve their mobile site
          performance and speed.
        </StyledParagraph>
      </Notification>
      <Notification margin={{ bottom: "16px" }}>
        <StyledParagraph margin={{ vertical: "0", horizontal: "0" }}>
          <strong>Our theme is not compatible</strong> with e-commerce,
          corporate, classifieds or custom WordPress sites.
        </StyledParagraph>
      </Notification>
      <RequestForm />
      <Box direction="row" justify="between" align="center">
        <Link>I already have a Site ID</Link>
        <Button
          primary
          label="Request Site ID"
          onClick={setSiteIdJustRequested}
        />
      </Box>
    </>
  );
};

WithoutSiteId.propTypes = {
  siteIdRequested: bool.isRequired,
  siteIdJustRequested: bool.isRequired,
  setSiteIdJustRequested: func.isRequired,
  saveSettings: func.isRequired
};

export default inject(({ stores: { settings, ui } }) => ({
  siteId: settings.site_id,
  siteIdRequested: settings.site_id_requested,
  siteIdJustRequested: ui.siteIdJustRequested,
  setSiteId: settings.setSiteId,
  setSiteIdJustRequested: ui.setSiteIdJustRequested,
  saveSettings: settings.saveSettings
}))(WithoutSiteId);

const StyledBox = styled(Box)`
  border-radius: 4px;
  background-color: #fff;
`;

const Container = styled(StyledBox)`
  box-shadow: 0 1px 4px 0 rgba(31, 56, 197, 0.12),
    0 8px 12px 0 rgba(31, 56, 197, 0.12);
`;

const Notification = styled(StyledBox)`
  padding: 8px;
  box-shadow: 0 1px 4px 0 rgba(31, 56, 197, 0.12);
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

const StyledParagraph = styled(Paragraph)`
  max-width: 100%;
`;

const Comment = styled(StyledParagraph)`
  color: #0c112b;
  opacity: 0.4;
`;

const Body = styled(Box)`
  padding: 20px 32px 32px 32px;
`;

const Separator = styled.div`
  width: 608px;
  height: 2px;
  opacity: 0.08;
  background-color: #1f38c5;
  margin-bottom: 40px;
`;

const Link = styled.a`
  color: #1f38c5;
  text-decoration: underline;
`;

const Image = styled.img`
  width: 224px;
  height: 397px;
  border-radius: 4px;
`;

const OuterContainer = styled.div`
  display: flex;
  width: 100%;
  margin: 40px 0;
`;

const InnerContainer = styled.div`
  display: flex;
  flex-direction: column;

  &:first-of-type {
    margin-right: 32px;
  }
`;

const ViewDemoButton = styled(Button)`
  width: 140px;
  align-self: flex-end;
`;
