import { mount, flushPromises } from "@vue/test-utils";
import { createPinia, setActivePinia } from "pinia";
import { describe, expect, it, vi } from "vitest";
import App from "../app.vue";

vi.mock("#imports", () => ({
    useCookie: vi.fn(() => ({
        value: null,
    })),
    navigateTo: vi.fn(),
}));

describe("App", () => {
    it("renders the insurance platform title", async () => {
        setActivePinia(createPinia());

        const wrapper = mount(
            {
                components: { App },
                template: "<Suspense><App /></Suspense>",
            },
            {
                global: {
                    stubs: {
                        NuxtPage: {
                            template: "<div>Insurance Platform</div>",
                        },
                    },
                },
            },
        );

        await flushPromises();

        expect(wrapper.text()).toContain("Insurance Platform");
    });
});
