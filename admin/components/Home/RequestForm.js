import React from "react";
import { string, func } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import {
  Box,
  Heading,
  Paragraph,
  FormField,
  TextInput,
  RadioButton
} from "grommet";

const RequestForm = ({
  requestFormName,
  requestFormEmail,
  requestFormUrl,
  requestFormType,
  requestFormTraffic,
  setRequestFormName,
  setRequestFormEmail,
  setRequestFormUrl,
  setRequestFormType,
  setRequestFormTraffic
}) => (
  <Container margin={{ bottom: "16px" }}>
    <Header margin={{ vertical: "0", horizontal: "0" }}>
      Get started by requesting a Site ID
    </Header>
    <Body>
      <Comment margin={{ top: "0", bottom: "20px" }}>
        Access to our platform is currently limited. In order to configure and
        activate our Progressive Web App (PWA) theme you have to request a site
        ID first. This will allow you to connect your WordPress site with our
        platform.
      </Comment>
      <FormField label="Name">
        <TextInput
          placeholder="John Doe"
          value={requestFormName}
          onChange={setRequestFormName}
        />
      </FormField>
      <FormField label="Email">
        <TextInput
          placeholder="johndoe@example.com"
          value={requestFormEmail}
          onChange={setRequestFormEmail}
        />
      </FormField>
      <FormField label="WordPress URL">
        <TextInput
          placeholder="yourblog.com"
          value={requestFormUrl}
          onChange={setRequestFormUrl}
        />
      </FormField>
      <Box margin={{ top: "24px" }} direction="row" justify="between">
        <RadioBox>
          <RadioHead>Type of your WordPress site</RadioHead>
          {[
            "Blog / News Site",
            "eCommerce / Online store",
            "Corporate site / Online bussiness",
            "Classifieds site",
            "Other"
          ].map(value => (
            <RadioButton
              key={value}
              label={value}
              name={value}
              checked={requestFormType === value}
              onChange={setRequestFormType}
            />
          ))}
        </RadioBox>
        <RadioBox>
          <RadioHead>Monthly traffic: (Pageviews per month)</RadioHead>
          {[
            "More than 1 million",
            "500.000 - 1 million",
            "100.000 - 500.000",
            "Less than 100.000",
            "I don't know"
          ].map(value => (
            <RadioButton
              key={value}
              label={value}
              name={value}
              checked={requestFormTraffic === value}
              onChange={setRequestFormTraffic}
            />
          ))}
        </RadioBox>
      </Box>
    </Body>
  </Container>
);

RequestForm.propTypes = {
  requestFormName: string.isRequired,
  requestFormEmail: string.isRequired,
  requestFormUrl: string.isRequired,
  requestFormType: string.isRequired,
  requestFormTraffic: string.isRequired,
  setRequestFormName: func.isRequired,
  setRequestFormEmail: func.isRequired,
  setRequestFormUrl: func.isRequired,
  setRequestFormType: func.isRequired,
  setRequestFormTraffic: func.isRequired
};

export default inject(({ stores: { ui } }) => ({
  requestFormName: ui.requestFormName,
  requestFormEmail: ui.requestFormEmail,
  requestFormUrl: ui.requestFormUrl,
  requestFormType: ui.requestFormType,
  requestFormTraffic: ui.requestFormTraffic,
  setRequestFormName: ui.setRequestFormName,
  setRequestFormEmail: ui.setRequestFormEmail,
  setRequestFormUrl: ui.setRequestFormUrl,
  setRequestFormType: ui.setRequestFormType,
  setRequestFormTraffic: ui.setRequestFormTraffic
}))(RequestForm);

const StyledBox = styled(Box)`
  border-radius: 4px;
  background-color: #fff;
`;

const Container = styled(StyledBox)`
  box-shadow: 0 1px 4px 0 rgba(31, 56, 197, 0.12),
    0 8px 12px 0 rgba(31, 56, 197, 0.12);
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

const RadioBox = styled(Box)`
  label[class^="StyledRadioButton"] {
    margin-bottom: 8px;
  }
`;

const RadioHead = styled(Paragraph)`
  width: 200px;
  margin-top: 0;
  margin-bottom: 12px;
`;
