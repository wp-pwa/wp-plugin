import { types, getSnapshot } from "mobx-state-tree";
import { post } from "axios";
import Store from "../settings";

jest.mock("axios");
jest.useFakeTimers();
window.ajaxurl = "https://ajax.url";

describe("Admin › Models › Settings", () => {
  afterEach(() => {
    post.mockClear();
    delete window.frontity;
  });

  test("Props should be populated correctly", () => {
    const store = Store.create();

    expect(store.site_id).toBe("");
    expect(store.site_id_requested).toBe(false);
    expect(store.pwa_active).toBe(false);
    expect(store.amp_active).toBe(false);
    expect(store.ssr_server).toBe("");
    expect(store.static_server).toBe("");
    expect(store.amp_server).toBe("");
    expect(store.frontpage_forced).toBe(false);
    expect(store.html_purifier_active).toBe(true);
    expect(store.excludes).toEqual([]);
    expect(store.injection_type).toBe("inline");
  });

  test("`general` should return the right value", () => {
    const store = types
      .model({
        settings: types.optional(Store, {}),
      })
      .create();

    Object.defineProperty(store, "general", {
      value: "general",
    });

    expect(store.settings.general).toBe("general");
  });

  test("`validations` should return the right value", () => {
    const store = types
      .model({
        settings: types.optional(Store, {}),
      })
      .create();

    Object.defineProperty(store, "validations", {
      value: { settings: "validations" },
    });

    expect(store.settings.validations).toBe("validations");
  });

  test("`setSiteId` should set a value for `site_id` and clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { site_id: "invalid", clear },
    });

    store.setSiteId({ target: { value: "1234567890" } });
    expect(store.site_id).toBe("1234567890");
    expect(clear).toHaveBeenCalledWith("site_id");
  });

  test("`setSiteId` should set a value for `site_id` but not to clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { clear },
    });

    store.setSiteId({ target: { value: "1234567890" } });
    expect(store.site_id).toBe("1234567890");
    expect(clear).not.toHaveBeenCalled();
  });

  test("`setPwaActive` should set a value for `pwa_active` and call `saveSettings()`", () => {
    const store = Store.create();
    const saveSettings = jest.fn();

    Object.defineProperty(store, "saveSettings", {
      value: saveSettings,
    });

    store.setPwaActive({ target: { checked: true } });
    expect(store.pwa_active).toBe(true);
    expect(saveSettings).toHaveBeenCalled();
  });

  test("`setAmpActive` should set a value for `amp_active` and call `saveSettings()`", () => {
    const store = Store.create();
    const saveSettings = jest.fn();

    Object.defineProperty(store, "saveSettings", {
      value: saveSettings,
    });

    store.setAmpActive({ target: { checked: true } });
    expect(store.amp_active).toBe(true);
    expect(saveSettings).toHaveBeenCalled();
  });

  test("`setSsrServer` should set a value for `ssr_server` and clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { ssr_server: "invalid", clear },
    });

    store.setSsrServer({ target: { value: "https://ssr.wp-pwa.com" } });
    expect(store.ssr_server).toBe("https://ssr.wp-pwa.com");
    expect(clear).toHaveBeenCalledWith("ssr_server");
  });

  test("`setSsrServer` should set a value for `ssr_server` but not to clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { clear },
    });

    store.setSsrServer({ target: { value: "https://ssr.wp-pwa.com" } });
    expect(store.ssr_server).toBe("https://ssr.wp-pwa.com");
    expect(clear).not.toHaveBeenCalled();
  });

  test("`setStaticServer` should set a value for `static_server` and clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { static_server: "invalid", clear },
    });

    store.setStaticServer({ target: { value: "https://static.wp-pwa.com" } });
    expect(store.static_server).toBe("https://static.wp-pwa.com");
    expect(clear).toHaveBeenCalledWith("static_server");
  });

  test("`setStaticServer` should set a value for `static_server` but not to clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { clear },
    });

    store.setStaticServer({ target: { value: "https://static.wp-pwa.com" } });
    expect(store.static_server).toBe("https://static.wp-pwa.com");
    expect(clear).not.toHaveBeenCalled();
  });

  test("`setAmpServer` should set a value for `amp_server` and clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { amp_server: "invalid", clear },
    });

    store.setAmpServer({ target: { value: "https://amp.wp-pwa.com" } });
    expect(store.amp_server).toBe("https://amp.wp-pwa.com");
    expect(clear).toHaveBeenCalledWith("amp_server");
  });

  test("`setAmpServer` should set a value for `amp_server` but not to clear the validation", () => {
    const store = Store.create();
    const clear = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { clear },
    });

    store.setAmpServer({ target: { value: "https://amp.wp-pwa.com" } });
    expect(store.amp_server).toBe("https://amp.wp-pwa.com");
    expect(clear).not.toHaveBeenCalled();
  });

  test("`setFrontpageForced` should set a value for `frontpage_forced`", () => {
    const store = Store.create();

    store.setFrontpageForced({ target: { checked: true } });
    expect(store.frontpage_forced).toBe(true);
  });

  test("`setHtmlPurifierActive` should set a value for `html_purifier_active`", () => {
    const store = Store.create();

    store.setHtmlPurifierActive({ target: { checked: false } });
    expect(store.html_purifier_active).toBe(false);
  });

  test("`setExclude` should set a value for `excludes`", () => {
    const store = Store.create();

    store.setExcludes({
      target: {
        value: "something\nsomething else",
      },
    });
    expect(store.excludes).toEqual(["something", "something else"]);
  });

  test("`setInjectionType` should set a value for `injection_type`", () => {
    const store = Store.create();

    store.setInjectionType({
      target: {
        value: "external",
      },
    });
    expect(store.injection_type).toEqual("external");
  });

  test("`setSiteIdRequested` should set a value for `site_id_requested`, clear the validation of `site_id` and call `saveSettings()`", () => {
    const store = Store.create();
    const saveSettings = jest.fn();
    const clear = jest.fn();

    Object.defineProperties(store, {
      saveSettings: { value: saveSettings },
      validations: { value: { site_id: "invalid", clear } },
    });

    store.setSiteIdRequested(true);
    expect(store.site_id_requested).toBe(true);
    expect(clear).toHaveBeenCalledWith("site_id");
    expect(saveSettings).toHaveBeenCalled();
  });

  test("`setSiteIdRequested` should set a value for `site_id_requested`, not to clear the validation of `site_id` and call `saveSettings()`", () => {
    const store = Store.create();
    const saveSettings = jest.fn();
    const clear = jest.fn();

    Object.defineProperties(store, {
      saveSettings: { value: saveSettings },
      validations: { value: { clear } },
    });

    store.setSiteIdRequested(true);
    expect(store.site_id_requested).toBe(true);
    expect(clear).not.toHaveBeenCalled();
    expect(saveSettings).toHaveBeenCalled();
  });

  test("`trimTextFields` should trim the text fields", async () => {
    const store = Store.create({
      site_id: "   1234567890   ",
      ssr_server: "   https://ssr.wp-pwa.com   ",
      static_server: "   https://static.wp-pwa.com   ",
      amp_server: "   https://amp.wp-pwa.com   ",
      excludes: ["   ", "   something   ", "   something else   "],
    });

    await store.trimTextFields();
    expect(store.site_id).toBe("1234567890");
    expect(store.ssr_server).toBe("https://ssr.wp-pwa.com");
    expect(store.static_server).toBe("https://static.wp-pwa.com");
    expect(store.amp_server).toBe("https://amp.wp-pwa.com");
    expect(store.excludes).toEqual(["something", "something else"]);
  });

  test("`saveSettings` should call `trimTextFields()`, send a request to save the settings and update `saveButtonStatus`", async () => {
    const store = Store.create();
    const validate = jest.fn(() => true);
    const setSaveButtonStatus = jest.fn();
    jest.spyOn(store, "trimTextFields");

    Object.defineProperties(store, {
      validate: { value: validate },
      general: { value: { setSaveButtonStatus } },
    });

    window.frontity = { plugin: { settings: {} } };

    const data = new window.FormData();
    data.append("action", "frontity_save_settings");
    data.append("data", JSON.stringify(getSnapshot(store)));

    await store.saveSettings();
    expect(store.trimTextFields).toHaveBeenCalled();
    expect(store.validate).toHaveBeenCalled();
    expect(post).toHaveBeenCalledWith("https://ajax.url", data);
    expect(window.frontity.plugin.settings).toBe(getSnapshot(store));
    jest.runOnlyPendingTimers();
    expect(setTimeout).toHaveBeenNthCalledWith(1, expect.any(Function), 500);
    expect(setTimeout).toHaveBeenNthCalledWith(2, expect.any(Function), 1000);
    jest.runOnlyPendingTimers();
    expect(setSaveButtonStatus).toHaveBeenNthCalledWith(1, "busy");
    expect(setSaveButtonStatus).toHaveBeenNthCalledWith(2, "done");
    expect(setSaveButtonStatus).toHaveBeenNthCalledWith(3, "idle");
  });

  test("`saveSettings` should call `trimTextFields()` and send a request to save the settings without validations", async () => {
    const store = Store.create({
      site_id_requested: true,
      pwa_active: true,
      amp_active: true,
    });
    const validate = jest.fn(() => false);
    jest.spyOn(store, "trimTextFields");

    Object.defineProperties(store, {
      validate: { value: validate },
    });

    window.frontity = {
      plugin: {
        settings: {
          site_id: "",
          site_id_requested: false,
          pwa_active: false,
          amp_active: false,
          ssr_server: "",
          static_server: "",
          amp_server: "",
          frontpage_forced: false,
          html_purifier_active: true,
          excludes: [],
          injection_type: "inline",
        },
      },
    };

    const mergedSettings = {
      site_id: "",
      site_id_requested: true,
      pwa_active: true,
      amp_active: true,
      ssr_server: "",
      static_server: "",
      amp_server: "",
      frontpage_forced: false,
      html_purifier_active: true,
      excludes: [],
      injection_type: "inline",
    };

    const data = new window.FormData();
    data.append("action", "frontity_save_settings");
    data.append("data", JSON.stringify(mergedSettings));

    await store.saveSettings();
    expect(store.trimTextFields).toHaveBeenCalled();
    expect(store.validate).toHaveBeenCalled();
    expect(post).toHaveBeenCalledWith("https://ajax.url", data);
    expect(window.frontity.plugin.settings).toEqual(mergedSettings);
  });

  test("`saveSettings` should call `trimTextFields()` but not to send a request to save settings", async () => {
    const store = Store.create();
    const validate = jest.fn(() => false);
    const setSaveButtonStatus = jest.fn();
    jest.spyOn(store, "trimTextFields");

    Object.defineProperties(store, {
      validate: { value: validate },
      general: { value: { setSaveButtonStatus } },
    });

    window.frontity = {
      plugin: {
        settings: {
          site_id: "",
          site_id_requested: false,
          pwa_active: false,
          amp_active: false,
          ssr_server: "",
          static_server: "",
          amp_server: "",
          frontpage_forced: false,
          html_purifier_active: true,
          excludes: [],
          injection_type: "inline",
        },
      },
    };

    await store.saveSettings();
    expect(store.trimTextFields).toHaveBeenCalled();
    expect(store.validate).toHaveBeenCalled();
    expect(post).not.toHaveBeenCalled();
  });

  test("`purgeHtmlPurifierCache` should send a request to purge the cache and update `purgeHtmlPurifierStatus`", async () => {
    const store = Store.create();
    const setPurgePurifierButtonStatus = jest.fn();

    Object.defineProperty(store, "general", {
      value: { setPurgePurifierButtonStatus },
    });

    const data = new window.FormData();
    data.append("action", "frontity_purge_htmlpurifier_cache");

    await store.purgeHtmlPurifierCache();
    expect(post).toHaveBeenCalledWith("https://ajax.url", data);
    jest.runOnlyPendingTimers();
    expect(setTimeout).toHaveBeenNthCalledWith(1, expect.any(Function), 500);
    expect(setTimeout).toHaveBeenNthCalledWith(2, expect.any(Function), 1000);
    jest.runOnlyPendingTimers();
    expect(setPurgePurifierButtonStatus).toHaveBeenNthCalledWith(1, "busy");
    expect(setPurgePurifierButtonStatus).toHaveBeenNthCalledWith(2, "done");
    expect(setPurgePurifierButtonStatus).toHaveBeenNthCalledWith(3, "idle");
  });

  test("`validate()` should call `validations.validate()`", () => {
    const store = Store.create();
    const validate = jest.fn();

    Object.defineProperty(store, "validations", {
      value: { validate },
    });

    store.validate();
    expect(validate).toHaveBeenCalled();
  });
});
