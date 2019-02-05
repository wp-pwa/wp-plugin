import React from "react";
import { string, func, shape } from "prop-types";
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
  name,
  email,
  url,
  type,
  traffic,
  nameValidation,
  emailValidation,
  urlValidation,
  typeValidation,
  trafficValidation,
  setName,
  setEmail,
  setUrl,
  setType,
  setTraffic,
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
          status={nameValidation}
          placeholder={requestFieldName.placeholder}
          value={name}
          onChange={setName}
        />
      </FormField>
      <FormField label={requestFieldEmail.label}>
        <StyledTextInput
          status={emailValidation}
          placeholder={requestFieldEmail.placeholder}
          value={email}
          onChange={setEmail}
        />
      </FormField>
      <FormField label={requestFieldUrl.label}>
        <StyledTextInput
          status={urlValidation}
          placeholder={requestFieldUrl.placeholder}
          value={url}
          onChange={setUrl}
        />
      </FormField>
      <RadioContainer
        margin={{ top: "24px" }}
        direction="row"
        justify="between"
      >
        <RadioBox status={typeValidation}>
          <RadioHead>{requestFieldType.label}</RadioHead>
          {Object.entries(requestFieldType.options).map(([option, label]) => (
            <RadioButton
              key={option}
              label={label}
              name={option}
              checked={type === option}
              onChange={setType}
            />
          ))}
        </RadioBox>
        <RadioBox status={trafficValidation}>
          <RadioHead>{requestFieldTraffic.label}</RadioHead>
          {Object.entries(requestFieldTraffic.options).map(
            ([option, label]) => (
              <RadioButton
                key={option}
                label={label}
                name={option}
                checked={traffic === option}
                onChange={setTraffic}
              />
            )
          )}
        </RadioBox>
      </RadioContainer>
    </Body>
  </Container>
);

RequestForm.propTypes = {
  name: string.isRequired,
  email: string.isRequired,
  url: string.isRequired,
  type: string.isRequired,
  traffic: string.isRequired,
  nameValidation: string,
  emailValidation: string,
  urlValidation: string,
  typeValidation: string,
  trafficValidation: string,
  setName: func.isRequired,
  setEmail: func.isRequired,
  setUrl: func.isRequired,
  setType: func.isRequired,
  setTraffic: func.isRequired,
  requestTitleText: string.isRequired,
  requestContentText: string.isRequired,
  requestFieldName: shape({ label: string, placeholder: string }).isRequired,
  requestFieldEmail: shape({ label: string, placeholder: string }).isRequired,
  requestFieldUrl: shape({ label: string, placeholder: string }).isRequired,
  requestFieldType: shape({
    label: string,
    options: shape({
      blog: string,
      ecommerce: string,
      corpsite: string,
      classifiedsite: string,
      other: string,
    }),
  }).isRequired,
  requestFieldTraffic: shape({
    label: string,
    options: shape({
      A: string,
      B: string,
      C: string,
      D: string,
      UNKNOWN: string,
    }),
  }).isRequired,
};

RequestForm.defaultProps = {
  nameValidation: undefined,
  emailValidation: undefined,
  urlValidation: undefined,
  typeValidation: undefined,
  trafficValidation: undefined,
};

export default inject(({ stores: { request, validations, languages } }) => {
  const requestForm = "home.withoutSiteId.requestForm";

  return {
    name: request.name,
    email: request.email,
    url: request.url,
    type: request.type,
    traffic: request.traffic,
    nameValidation: validations.request.name,
    emailValidation: validations.request.email,
    urlValidation: validations.request.url,
    typeValidation: validations.request.type,
    trafficValidation: validations.request.traffic,
    setName: request.setName,
    setEmail: request.setEmail,
    setUrl: request.setUrl,
    setType: request.setType,
    setTraffic: request.setTraffic,
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

const RadioContainer = styled(Box)`
  @media (max-width: 600px) {
    flex-direction: column;
  }
`;

const RadioBox = styled(Box)`
  &:first-of-type {
    @media (max-width: 600px) {
      margin-bottom: 16px;
    }
  }

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

  @media (max-width: 600px) {
    width: auto;
  }
`;

const StyledTextInput = styled(TextInput)`
  ${({ status }) =>
    status === "invalid" ? "background-color: #ea5a3555;" : ""}
`;
