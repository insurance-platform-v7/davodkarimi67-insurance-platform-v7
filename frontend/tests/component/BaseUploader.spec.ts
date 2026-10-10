import { mount } from "@vue/test-utils";
import { describe, expect, it } from "vitest";
import BaseUploader from "~/components/BaseUploader.vue";

describe("BaseUploader", () => {
    it("emits change and shows progress for a valid file", async () => {
        const wrapper = mount(BaseUploader);
        const file = new File(["content"], "test.txt", { type: "text/plain" });
        const input = wrapper.get("input[type='file']");

        Object.defineProperty(input.element, "files", {
            value: [file],
        });

        await input.trigger("change");

        expect(wrapper.emitted("change")).toHaveLength(1);
        expect(wrapper.find("progress").exists()).toBe(true);
    });

    it("emits invalid for an oversized file", async () => {
        const wrapper = mount(BaseUploader, {
            props: {
                maxSize: 1,
            },
        });

        const file = new File(["too large"], "large.txt");
        const input = wrapper.get("input[type='file']");

        Object.defineProperty(input.element, "files", {
            value: [file],
        });

        await input.trigger("change");

        expect(wrapper.emitted("invalid")).toHaveLength(1);
        expect(wrapper.get('[role="alert"]').text()).toBe("File is too large");
    });

    it("accepts a dropped file", async () => {
        const wrapper = mount(BaseUploader);
        const file = new File(["content"], "drop.txt");

        await wrapper.get('[role="button"]').trigger("drop", {
            dataTransfer: {
                files: [file],
            },
        });

        expect(wrapper.emitted("change")).toHaveLength(1);
    });
});