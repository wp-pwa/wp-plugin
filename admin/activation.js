import { getSnapshot } from "mobx-state-tree";
import Settings from "./models";

const settings = getSnapshot(Settings.create());

console.log("settings:", settings);
