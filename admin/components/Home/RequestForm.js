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
        <TextInput
          placeholder={requestFieldName.placeholder}
          value={requestFormName}
          onChange={setRequestFormName}
        />
      </FormField>
      <FormField label={requestFieldEmail.label}>
        <TextInput
          placeholder={requestFieldEmail.placeholder}
          value={requestFormEmail}
          onChange={setRequestFormEmail}
        />
      </FormField>
      <FormField label={requestFieldUrl.label}>
        <TextInput
          placeholder={requestFieldUrl.placeholder}
          value={requestFormUrl}
          onChange={setRequestFormUrl}
        />
      </FormField>
      <Box margin={{ top: "24px" }} direction="row" justify="between">
        <RadioBox>
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
        <RadioBox>
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

export default inject(({ stores: { ui, languages } }) => {
  const requestForm = "home.withoutSiteId.requestForm";

  return {
    requestFormName: ui.requestFormName,
    requestFormEmail: ui.requestFormEmail,
    requestFormUrl: ui.requestFormUrl,
    requestFormType: ui.requestFormType,
    requestFormTraffic: ui.requestFormTraffic,
    setRequestFormName: ui.setRequestFormName,
    setRequestFormEmail: ui.setRequestFormEmail,
    setRequestFormUrl: ui.setRequestFormUrl,
    setRequestFormType: ui.setRequestFormType,
    setRequestFormTraffic: ui.setRequestFormTraffic,
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
`;

const RadioHead = styled(Paragraph)`
  width: 200px;
  margin-top: 0;
  margin-bottom: 12px;
`;
