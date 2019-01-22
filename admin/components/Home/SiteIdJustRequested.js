/* eslint-disable jsx-a11y/anchor-is-valid */
import React from "react";
import { string, func } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import { Box, Heading, Paragraph } from "grommet";

const SiteIdJustRequested = ({
  emojiText,
  titleText,
  contentText,
  linkText,
  setSiteIdJustRequested,
}) => (
  <>
    <Container margin={{ top: "40px", bottom: "16px" }}>
      <Header margin={{ horizontal: "0", vertical: "0" }}>
        <span aria-label="Party emoji" role="img">
          {emojiText}
        </span>
        {titleText}
      </Header>
      <Body>
        <StyledParagraph margin={{ vertical: "0", horizontal: "0" }}>
          {contentText}
        </StyledParagraph>
      </Body>
    </Container>
    <Link onClick={setSiteIdJustRequested}>{linkText}</Link>
  </>
);

SiteIdJustRequested.propTypes = {
  emojiText: string.isRequired,
  titleText: string.isRequired,
  contentText: string.isRequired,
  linkText: string.isRequired,
  setSiteIdJustRequested: func.isRequired,
};

export default inject(({ stores: { general, languages } }) => {
  const siteIdJustRequested = "home.siteIdJustRequested";

  return {
    setSiteIdJustRequested: () => {
      general.setSiteIdJustRequested(false);
    },
    emojiText: languages.get(`${siteIdJustRequested}.emoji`),
    titleText: languages.get(`${siteIdJustRequested}.title`),
    contentText: languages.get(`${siteIdJustRequested}.content`),
    linkText: languages.get(`${siteIdJustRequested}.link`),
  };
})(SiteIdJustRequested);

const Container = styled(Box)`
  border-radius: 4px;
  background-color: #fff;
  box-shadow: 0 1px 4px 0 rgba(31, 56, 197, 0.12),
    0 8px 12px 0 rgba(31, 56, 197, 0.12);
`;

const Header = styled(Heading)`
  display: block;
  background-color: #f6f9fa;
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  padding: 32px;
  font-size: 24px;
  font-weight: 600;

  & > span {
    margin-right: 5px;
  }
`;

const Body = styled(Box)`
  padding: 20px 32px 32px 32px;
`;

const StyledParagraph = styled(Paragraph)`
  max-width: 100%;
`;

const Link = styled.a`
  color: #1f38c5;
  text-decoration: underline;
  margin: auto;
`;
