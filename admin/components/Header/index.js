import React from "react";
import styled from "styled-components";
import { Box, Paragraph } from "grommet";
import Logo from "./Logo";
import Links from "./Links";

const Header = () => (
  <>
    <StyledBox height="64px" direction="row" align="center" justify="between">
      <Logo />
      <Links />
    </StyledBox>
    <DeprecatedBox>
      <Paragraph>This plugin will no longer be maintained!</Paragraph>
      <Paragraph>
        {
          "We are working on a new open source framework for creating WordPress sites with ReactJS. If you want to learn more, please visit "
        }
        <a
          href="https://frontity.org/?utm_source=wp-pwa-plugin&utm_medium=frontity-link&utm_campaign=pre-launch"
          rel="noopener noreferrer"
          target="_blank"
        >
          frontity.org
        </a>
        {"."}
      </Paragraph>
    </DeprecatedBox>
  </>
);

export default Header;

const StyledBox = styled(Box)`
  background: #fff;
  box-shadow: 0 1px 4px 0 rgba(31, 56, 197, 0.12);
  padding: 0 32px;
  flex-wrap: wrap;

  @media (max-width: 782px) {
    flex-direction: column;
    align-items: center;
    padding: 12px;
    height: auto;

    & > * {
      margin: 8px 0;
    }
  }
`;

const DeprecatedBox = styled(Box)`
  width: 608px;
  margin: auto;
  margin-top: 24px;
  align-items: center;
  justify-content: center;
  border: 2px solid #ff0000;
  background-color: #ff000033;
  border-radius: 4px;
  padding: 16px 0;

  a {
    color: rgb(31, 56, 197);
  }

  p {
    margin: 8px 0;
  }

  p:nth-of-type(1) {
    font-weight: bold;
  }
`;
