import { types } from "mobx-state-tree";
import Validations from "../validations";
import Request from "../validations/request";

describe("Admin › Models › Validations › Request", () => {
  test("Props should be populated correctly", () => {
    const store = Request.create();

    expect(store.name).toBeUndefined();
    expect(store.email).toBeUndefined();
    expect(store.url).toBeUndefined();
    expect(store.type).toBeUndefined();
    expect(store.traffic).toBeUndefined();
  });

  test("`validations` should return the right value", () => {
    const store = types
      .model({
        validations: types.optional(Validations, {}),
      })
      .create();

    expect(store.validations.request.validations).toBe(store.validations);
  });

  test("`request` should return the right value", () => {
    const store = types
      .model({
        validations: types.optional(Validations, {}),
      })
      .create();

    Object.defineProperty(store, "request", {
      value: "request",
    });

    expect(store.validations.request.request).toBe("request");
  });

  test("`nameIsValid` should return the right value", () => {
    const store = Request.create();

    Object.defineProperty(store, "request", {
      value: { name: "Name" },
    });

    expect(store.nameIsValid).toBe(true);
  });

  test("`emailIsValid` should return the right value", () => {
    const store = Request.create();

    Object.defineProperty(store, "request", {
      value: { email: "john@doe.com" },
    });

    expect(store.emailIsValid).toBe(true);
  });

  test("`urlIsValid` should return the right value", () => {
    const store = Request.create();

    Object.defineProperty(store, "request", {
      value: { url: "https://johndoe.com" },
    });

    expect(store.urlIsValid).toBe(true);
  });

  test("`typeIsValid` should return the right value", () => {
    const store = Request.create();

    Object.defineProperty(store, "request", {
      value: { type: "Some Value" },
    });

    expect(store.typeIsValid).toBe(true);
  });

  test("`trafficIsValid` should return the right value", () => {
    const store = Request.create();

    Object.defineProperty(store, "request", {
      value: { traffic: "Some Value" },
    });

    expect(store.trafficIsValid).toBe(true);
  });

  test("`clear()` should call `validations.clear()`", () => {
    const store = Request.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { clear },
    });

    store.clear("name");
    expect(clear).toHaveBeenCalledWith("request", "name");
  });

  test("`validate()` should call `validations.validateAll()` and return a boolean", () => {
    const store = Request.create();
    const validateAll = jest.fn(() => false);

    Object.defineProperty(store, "validations", {
      value: { validateAll },
    });

    const validation = store.validate();
    expect(validateAll).toHaveBeenCalledWith("request");
    expect(validation).toEqual(expect.any(Boolean));
  });
});
