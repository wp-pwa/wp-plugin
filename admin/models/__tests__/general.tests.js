import Store from "../general";

describe("Admin › Stores › General", () => {
  test("Props should be populated correcly", () => {
    const store = Store.create({
      site: "http://blog.frontity.io",
      page: "frontity-dashboard",
    });

    expect(store.site).toBe("http://blog.frontity.io");
    expect(store.page).toBe("frontity-dashboard");
    expect(store.siteIdJustRequested).toBe(false);
    expect(store.saveButtonStatus).toBe("idle");
  });

  test("`setSaveButtonStatus` should set `saveButtonStatus` value", () => {
    const store = Store.create();

    expect(store.saveButtonStatus).toBe("idle");
    store.setSaveButtonStatus("busy");
    expect(store.saveButtonStatus).toBe("busy");
  });

  test("`setSiteIdJustRequested` should set `siteIdJustRequested` value", () => {
    const store = Store.create();

    expect(store.siteIdJustRequested).toBe(false);
    store.setSiteIdJustRequested(true);
    expect(store.siteIdJustRequested).toBe(true);
  });
});
