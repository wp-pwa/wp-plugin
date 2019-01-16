import Store from "../languages";
import languages from "../../languages";

describe("Admin › Models › Languages", () => {
  test("Props should be populated correctly", () => {
    const store = Store.create({ code: "en_US" });

    expect(store.code).toBe("en_US");
    expect(store.current).toEqual(languages.en_US);
    expect(store.default).toEqual(languages.en_US);
  });

  test("Current should be an empty object", () => {
    const store = Store.create({ code: "es_ES" });

    expect(store.code).toBe("es_ES");
    expect(store.current).toEqual({});
    expect(store.default).toEqual(languages.en_US);
  });

  test("`get` should return the right value from current", () => {
    const store = Store.create();

    expect(store.code).toBe("en_US");
    const text = store.get("header.links.contactAndHelp");
    expect(text).toBe("Contact and help");
    const links = store.get("header.links");
    expect(links).toEqual({
      contactAndHelp: "Contact and help",
      documentation: "Documentation",
      viewDemo: "View demo",
    });
  });

  test("`get` should return the right value from default", () => {
    const store = Store.create({ code: "es_ES" });

    expect(store.code).toBe("es_ES");
    const text = store.get("header.links.contactAndHelp");
    expect(text).toBe("Contact and help");
    const links = store.get("header.links");
    expect(links).toEqual({
      contactAndHelp: "Contact and help",
      documentation: "Documentation",
      viewDemo: "View demo",
    });
  });
});
