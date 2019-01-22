import Validations from "../validations";

describe("Admin › Models › Validations", () => {
  test("`clear()` should clear a validation", () => {
    const store = Validations.create({
      settings: {
        site_id: "valid",
      },
    });

    store.clear("settings", "site_id");
    expect(store.settings.site_id).toBeUndefined();
  });

  test("`validate()` should set 'valid' for a validation and return 'true'", () => {
    const store = Validations.create();

    Object.defineProperty(store.request, "nameIsValid", {
      value: true,
    });

    const validation = store.validate("request", "name");
    expect(store.request.name).toBe("valid");
    expect(validation).toBe(true);
  });

  test("`validate()` should set 'invalid' for a validation and return 'false'", () => {
    const store = Validations.create();

    Object.defineProperty(store.request, "nameIsValid", {
      value: false,
    });

    const validation = store.validate("request", "name");
    expect(store.request.name).toBe("invalid");
    expect(validation).toBe(false);
  });

  test("`validateAll()` should validate all the props and return 'true'", () => {
    const store = Validations.create();
    const validate = jest.fn(() => true);

    Object.defineProperty(store, "validate", {
      value: validate,
    });

    const validation = store.validateAll("settings");
    expect(validate).toHaveBeenCalledTimes(4);
    expect(validate).toHaveBeenNthCalledWith(1, "settings", "site_id");
    expect(validate).toHaveBeenNthCalledWith(2, "settings", "ssr_server");
    expect(validate).toHaveBeenNthCalledWith(3, "settings", "static_server");
    expect(validate).toHaveBeenNthCalledWith(4, "settings", "amp_server");
    expect(validation).toBe(true);
  });

  test("`validateAll()` should validate all the props and return 'false'", () => {
    const store = Validations.create();
    const validate = jest.fn((_model, field) => field === "site_id");

    Object.defineProperty(store, "validate", {
      value: validate,
    });

    const validation = store.validateAll("settings");
    expect(validate).toHaveBeenCalledTimes(4);
    expect(validate).toHaveBeenNthCalledWith(1, "settings", "site_id");
    expect(validate).toHaveBeenNthCalledWith(2, "settings", "ssr_server");
    expect(validate).toHaveBeenNthCalledWith(3, "settings", "static_server");
    expect(validate).toHaveBeenNthCalledWith(4, "settings", "amp_server");
    expect(validation).toBe(false);
  });
});
