import React from "react";
import { string } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import { Box, Heading, Paragraph } from "grommet";

const SiteIdJustRequested = ({ emojiText, titleText, contentText }) => (
  <Container margin={{ top: "40px" }}>
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
);

SiteIdJustRequested.propTypes = {
  emojiText: string.isRequired,
  titleText: string.isRequired,
  contentText: string.isRequired,
};

export default inject(({ stores: { languages } }) => {
  const siteIdJustRequested = "home.siteIdJustRequested";

  return {
    emojiText: languages.get(`${siteIdJustRequested}.emoji`),
    titleText: languages.get(`${siteIdJustRequested}.title`),
    contentText: languages.get(`${siteIdJustRequested}.content`),
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

const Body = styled(Box)`
  padding: 20px 32px 32px 32px;
`;

const StyledParagraph = styled(Paragraph)`
  max-width: 100%;
`;
