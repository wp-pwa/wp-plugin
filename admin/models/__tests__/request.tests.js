import { types } from "mobx-state-tree";
import { post } from "axios";
import Store from "../request";

jest.mock("axios");

describe("Admin › Models › Request", () => {
  afterEach(() => {
    post.mockClear();
  });

  test("Props should be populated correctly", () => {
    const store = Store.create();

    expect(store.name).toBe("");
    expect(store.email).toBe("");
    expect(store.url).toBe("");
    expect(store.type).toBe("");
    expect(store.traffic).toBe("");
  });

  test("`root` should return the right value", () => {
    const store = types
      .model({
        request: types.optional(Store, {}),
      })
      .create();

    expect(store.request.root).toBe(store);
  });

  test("`validations` should return the right value", () => {
    const store = types
      .model({
        request: types.optional(Store, {}),
      })
      .create();

    Object.defineProperty(store, "validations", {
      value: { request: "request" },
    });

    expect(store.request.validations).toBe("request");
  });

  test("`setName` should set a value for `name` and clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { name: "invalid", clear },
    });

    store.setName({ target: { value: "John Snow" } });
    expect(store.name).toBe("John Snow");
    expect(store.validations.clear).toHaveBeenCalledWith("name");
  });

  test("`setName` should set a value for `name` but not to clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { clear },
    });

    store.setName({ target: { value: "John Snow" } });
    expect(store.name).toBe("John Snow");
    expect(store.validations.clear).not.toHaveBeenCalled();
  });

  test("`setEmail` should set a value for `email` and clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { email: "invalid", clear },
    });

    store.setEmail({ target: { value: "john@snow.com" } });
    expect(store.email).toBe("john@snow.com");
    expect(store.validations.clear).toHaveBeenCalledWith("email");
  });

  test("`setEmail` should set a value for `email` but not to clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { clear },
    });

    store.setEmail({ target: { value: "john@snow.com" } });
    expect(store.email).toBe("john@snow.com");
    expect(store.validations.clear).not.toHaveBeenCalled();
  });

  test("`setUrl` should set a value for `url` and clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { url: "invalid", clear },
    });

    store.setUrl({ target: { value: "https://johnsnow.com" } });
    expect(store.url).toBe("https://johnsnow.com");
    expect(store.validations.clear).toHaveBeenCalledWith("url");
  });

  test("`setUrl` should set a value for `url` but not to clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { clear },
    });

    store.setUrl({ target: { value: "https://johnsnow.com" } });
    expect(store.url).toBe("https://johnsnow.com");
    expect(store.validations.clear).not.toHaveBeenCalled();
  });

  test("`setType` should set a value for `type` and clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { type: "invalid", clear },
    });

    store.setType({ target: { name: "Blog / News Site" } });
    expect(store.type).toBe("Blog / News Site");
    expect(store.validations.clear).toHaveBeenCalledWith("type");
  });

  test("`setType` should set a value for `type` but not to clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { clear },
    });

    store.setType({ target: { name: "Blog / News Site" } });
    expect(store.type).toBe("Blog / News Site");
    expect(store.validations.clear).not.toHaveBeenCalled();
  });

  test("`setTraffic` should set a value for `traffic` and clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { traffic: "invalid", clear },
    });

    store.setTraffic({ target: { name: "More than 1 million" } });
    expect(store.traffic).toBe("More than 1 million");
    expect(store.validations.clear).toHaveBeenCalledWith("traffic");
  });

  test("`setTraffic` should set a value for `traffic` but not to clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { clear },
    });

    store.setTraffic({ target: { name: "More than 1 million" } });
    expect(store.traffic).toBe("More than 1 million");
    expect(store.validations.clear).not.toHaveBeenCalled();
  });

  test("`sendRequest` should trim the text fields", () => {
    const store = Store.create({
      name: "   Some Body   ",
      email: "   some@body.com   ",
      url: "   https://somebody.com   ",
    });
    const validate = jest.fn(() => false);

    Object.defineProperty(store, "validate", {
      value: validate,
    });

    expect(store.name).toBe("   Some Body   ");
    expect(store.email).toBe("   some@body.com   ");
    expect(store.url).toBe("   https://somebody.com   ");
    store.sendRequest();
    expect(store.name).toBe("Some Body");
    expect(store.email).toBe("some@body.com");
    expect(store.url).toBe("https://somebody.com");
  });

  test("`sendRequest` should send a request with the right values", async () => {
    const store = Store.create({
      name: "Harry Potter",
      email: "harrypotter@hogwarts.com",
      url: "https://harrypotter.com",
      type: "blog",
      traffic: "A",
    });
    const validate = jest.fn(() => true);
    const setSiteIdJustRequested = jest.fn();
    const setSiteIdRequested = jest.fn();

    Object.defineProperties(store, {
      validate: { value: validate },
      root: {
        value: {
          general: { setSiteIdJustRequested },
          settings: { setSiteIdRequested },
        },
      },
    });

    await store.sendRequest();
    expect(validate).toHaveBeenCalled();
    expect(post).toHaveBeenCalledWith(
      "https://hook.integromat.com/214srcvxlj88frdnqaua6vipqvsnmjgo",
      {
        name: "Harry Potter",
        email: "harrypotter@hogwarts.com",
        url: "https://harrypotter.com",
        type: "blog",
        traffic: "A",
        origin: "plugin",
      }
    );
    expect(setSiteIdJustRequested).toHaveBeenCalledWith(true);
    expect(setSiteIdRequested).toHaveBeenCalledWith(true);
  });

  test("`sendRequest` should not send a request if data is not valid", async () => {
    const store = Store.create();
    const validate = jest.fn(() => false);

    Object.defineProperties(store, {
      validate: { value: validate },
    });

    await store.sendRequest();
    expect(validate).toHaveBeenCalled();
    expect(post).not.toHaveBeenCalled();
  });

  test("`validate` should call `validations.validate()`", () => {
    const store = Store.create();
    const validate = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { validate },
    });

    store.validate();
    expect(validate).toHaveBeenCalled();
  });
});
