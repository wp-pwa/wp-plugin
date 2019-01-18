import Store from "../general";

describe("Admin › Models › General", () => {
  test("Props should be populated correcly", () => {
    const store = Store.create({
      pluginDirUrl: "http://blog.frontity.io/wp-content/plugins/wp-pwa",
      page: "frontity-dashboard",
    });

    expect(store.pluginDirUrl).toBe(
      "http://blog.frontity.io/wp-content/plugins/wp-pwa"
    );
    expect(store.page).toBe("frontity-dashboard");
    expect(store.siteIdJustRequested).toBe(false);
    expect(store.saveButtonStatus).toBe("idle");
    expect(store.purgePurifierButtonStatus).toBe("idle");
  });

  test("`setSiteIdJustRequested` should set a value for `siteIdJustRequested`", () => {
    const store = Store.create();

    expect(store.siteIdJustRequested).toBe(false);
    store.setSiteIdJustRequested(true);
    expect(store.siteIdJustRequested).toBe(true);
  });

  test("`setSaveButtonStatus` should set a value for `saveButtonStatus`", () => {
    const store = Store.create();

    expect(store.saveButtonStatus).toBe("idle");
    store.setSaveButtonStatus("busy");
    expect(store.saveButtonStatus).toBe("busy");
  });

  test("`setPurgePurifierButtonStatus` should set a value for `purgePurifierButtonStatus`", () => {
    const store = Store.create();

    expect(store.purgePurifierButtonStatus).toBe("idle");
    store.setPurgePurifierButtonStatus("busy");
    expect(store.purgePurifierButtonStatus).toBe("busy");
  });
});
