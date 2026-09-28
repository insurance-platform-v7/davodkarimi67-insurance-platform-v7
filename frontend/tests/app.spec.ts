import { mount } from "@vue/test-utils";
import { describe, expect, it } from "vitest";
import App from "../app.vue";

describe("App", () => {
    it("renders the insurance platform title", () => {
        const wrapper = mount(App);

        expect(wrapper.text()).toBe("Insurance Platform");
    });
});
