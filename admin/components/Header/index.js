import React from "react";
import styled from "styled-components";
import { Box } from "grommet";
import Logo from "./Logo";
import Links from "./Links";

const Header = () => (
  <StyledBox height="64px" direction="row" align="center" justify="between">
    <Logo />
    <Links />
  </StyledBox>
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
