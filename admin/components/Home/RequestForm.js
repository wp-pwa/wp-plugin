import React from "react";
import { string, func, shape, arrayOf } from "prop-types";
import { inject } from "mobx-react";
import styled from "styled-components";
import {
  Box,
  Heading,
  Paragraph,
  FormField,
  TextInput,
  RadioButton,
} from "grommet";

const RequestForm = ({
  requestFormName,
  requestFormEmail,
  requestFormUrl,
  requestFormType,
  requestFormTraffic,
  requestFormNameStatus,
  requestFormEmailStatus,
  requestFormUrlStatus,
  requestFormTypeStatus,
  requestFormTrafficStatus,
  setRequestFormName,
  setRequestFormEmail,
  setRequestFormUrl,
  setRequestFormType,
  setRequestFormTraffic,
  requestTitleText,
  requestContentText,
  requestFieldName,
  requestFieldEmail,
  requestFieldUrl,
  requestFieldType,
  requestFieldTraffic,
}) => (
  <Container margin={{ bottom: "16px" }}>
    <Header margin={{ vertical: "0", horizontal: "0" }}>
      {requestTitleText}
    </Header>
    <Body>
      <Comment margin={{ top: "0", bottom: "20px" }}>
        {requestContentText}
      </Comment>
      <FormField label={requestFieldName.label}>
        <StyledTextInput
          status={requestFormNameStatus}
          placeholder={requestFieldName.placeholder}
          value={requestFormName}
          onChange={setRequestFormName}
        />
      </FormField>
      <FormField label={requestFieldEmail.label}>
        <StyledTextInput
          status={requestFormEmailStatus}
          placeholder={requestFieldEmail.placeholder}
          value={requestFormEmail}
          onChange={setRequestFormEmail}
        />
      </FormField>
      <FormField label={requestFieldUrl.label}>
        <StyledTextInput
          status={requestFormUrlStatus}
          placeholder={requestFieldUrl.placeholder}
          value={requestFormUrl}
          onChange={setRequestFormUrl}
        />
      </FormField>
      <Box margin={{ top: "24px" }} direction="row" justify="between">
        <RadioBox status={requestFormTypeStatus}>
          <RadioHead>{requestFieldType.label}</RadioHead>
          {requestFieldType.options.map(value => (
            <RadioButton
              key={value}
              label={value}
              name={value}
              checked={requestFormType === value}
              onChange={setRequestFormType}
            />
          ))}
        </RadioBox>
        <RadioBox status={requestFormTrafficStatus}>
          <RadioHead>{requestFieldTraffic.label}</RadioHead>
          {requestFieldTraffic.options.map(option => (
            <RadioButton
              key={option}
              label={option}
              name={option}
              checked={requestFormTraffic === option}
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
  requestFormNameStatus: string,
  requestFormEmailStatus: string,
  requestFormUrlStatus: string,
  requestFormTypeStatus: string,
  requestFormTrafficStatus: string,
  setRequestFormName: func.isRequired,
  setRequestFormEmail: func.isRequired,
  setRequestFormUrl: func.isRequired,
  setRequestFormType: func.isRequired,
  setRequestFormTraffic: func.isRequired,
  requestTitleText: string.isRequired,
  requestContentText: string.isRequired,
  requestFieldName: shape({ label: string, placeholder: string }).isRequired,
  requestFieldEmail: shape({ label: string, placeholder: string }).isRequired,
  requestFieldUrl: shape({ label: string, placeholder: string }).isRequired,
  requestFieldType: shape({ label: string, options: arrayOf(string) })
    .isRequired,
  requestFieldTraffic: shape({ label: string, options: arrayOf(string) })
    .isRequired,
};

RequestForm.defaultProps = {
  requestFormNameStatus: undefined,
  requestFormEmailStatus: undefined,
  requestFormUrlStatus: undefined,
  requestFormTypeStatus: undefined,
  requestFormTrafficStatus: undefined,
};

export default inject(({ stores: { ui, languages } }) => {
  const requestForm = "home.withoutSiteId.requestForm";

  return {
    requestFormName: ui.requestFormName,
    requestFormEmail: ui.requestFormEmail,
    requestFormUrl: ui.requestFormUrl,
    requestFormType: ui.requestFormType,
    requestFormTraffic: ui.requestFormTraffic,
    requestFormNameStatus: ui.requestFormNameStatus,
    requestFormEmailStatus: ui.requestFormEmailStatus,
    requestFormUrlStatus: ui.requestFormUrlStatus,
    requestFormTypeStatus: ui.requestFormTypeStatus,
    requestFormTrafficStatus: ui.requestFormTrafficStatus,
    setRequestFormName: event => {
      ui.setRequestFormNameStatus();
      ui.setRequestFormName(event);
    },
    setRequestFormEmail: event => {
      ui.setRequestFormEmailStatus();
      ui.setRequestFormEmail(event);
    },
    setRequestFormUrl: event => {
      ui.setRequestFormUrlStatus();
      ui.setRequestFormUrl(event);
    },
    setRequestFormType: event => {
      ui.setRequestFormTypeStatus();
      ui.setRequestFormType(event);
    },
    setRequestFormTraffic: event => {
      ui.setRequestFormTrafficStatus();
      ui.setRequestFormTraffic(event);
    },
    requestTitleText: languages.get(`${requestForm}.title`),
    requestContentText: languages.get(`${requestForm}.content`),
    requestFieldName: languages.get(`${requestForm}.fieldName`),
    requestFieldEmail: languages.get(`${requestForm}.fieldEmail`),
    requestFieldUrl: languages.get(`${requestForm}.fieldUrl`),
    requestFieldType: languages.get(`${requestForm}.fieldType`),
    requestFieldTraffic: languages.get(`${requestForm}.fieldTraffic`),
  };
})(RequestForm);

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
  div[class^="StyledRadioButton__StyledRadioButtonBox"] {
    ${({ status }) =>
      status === "invalid" ? "background-color: #ea5a3555;" : ""}
  }
`;

const RadioHead = styled(Paragraph)`
  width: 200px;
  margin-top: 0;
  margin-bottom: 12px;
`;

const StyledTextInput = styled(TextInput)`
  ${({ status }) =>
    status === "invalid" ? "background-color: #ea5a3555;" : ""}
`;
