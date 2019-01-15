import { types } from "mobx-state-tree";
import Validations from "../validations";
import Settings from "../validations/settings";

describe("Admin › Models › Validations › Settings", () => {
  test("Props should be populated correctly", () => {
    const store = Settings.create();

    expect(store.site_id).toBeUndefined();
    expect(store.ssr_server).toBeUndefined();
    expect(store.static_server).toBeUndefined();
    expect(store.amp_server).toBeUndefined();
  });

  test("`validations` should return the right value", () => {
    const store = types
      .model({
        validations: types.optional(Validations, {}),
      })
      .create();

    expect(store.validations.settings.validations).toBe(store.validations);
  });

  test("`settings` should return the right value", () => {
    const store = types
      .model({
        validations: types.optional(Validations, {}),
      })
      .create();

    Object.defineProperty(store, "settings", {
      value: "settings",
    });

    expect(store.validations.settings.settings).toBe("settings");
  });

  test("`site_idIsValid()` should return the right value", () => {
    const store = Settings.create();

    Object.defineProperty(store, "settings", {
      value: { site_id: "1234567890" },
    });

    expect(store.site_idIsValid).toBe(true);
  });

  test("`ssr_serverIsValid()` should return the right value", () => {
    const store = Settings.create();

    Object.defineProperty(store, "settings", {
      value: { ssr_server: "https://ssr.wp-pwa.com" },
    });
    expect(store.ssr_serverIsValid).toBe(true);
  });

  test("`static_serverIsValid()` should return the right value", () => {
    const store = Settings.create();

    Object.defineProperty(store, "settings", {
      value: { static_server: "https://static.wp-pwa.com" },
    });
    expect(store.static_serverIsValid).toBe(true);
  });

  test("`amp_serverIsValid()` should return the right value", () => {
    const store = Settings.create();

    Object.defineProperty(store, "settings", {
      value: { amp_server: "https://amp.wp-pwa.com" },
    });
    expect(store.amp_serverIsValid).toBe(true);
  });

  test("`clear()` should call `validations.clear()`", () => {
    const store = Settings.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { clear },
    });

    store.clear("site_id");
    expect(clear).toHaveBeenCalledWith("settings", "site_id");
  });

  test("`validate()` should call `validations.validateAll()` and return a boolean", () => {
    const store = Settings.create();
    const validateAll = jest.fn(() => false);

    Object.defineProperty(store, "validations", {
      value: { validateAll },
    });

    const validation = store.validate();
    expect(validateAll).toHaveBeenCalledWith("settings");
    expect(validation).toEqual(expect.any(Boolean));
  });
});
