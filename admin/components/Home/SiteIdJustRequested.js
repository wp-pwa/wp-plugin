import React from "react";
import styled from "styled-components";
import { Box, Heading, Paragraph } from "grommet";

const SiteIdJustRequested = () => (
  <Container margin={{ top: "40px" }}>
    <Header margin={{ horizontal: "0", vertical: "0" }}>
      <span aria-label="Party emoji" role="img">
        🎉
      </span>
      We have received your request, thanks!
    </Header>
    <Body>
      <StyledParagraph margin={{ vertical: "0", horizontal: "0" }}>
        Our team will manually review your request and get back to you shortly.
      </StyledParagraph>
    </Body>
  </Container>
);

export default SiteIdJustRequested;

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
