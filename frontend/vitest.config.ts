import { defineConfig } from "vitest/config";
import vue from "@vitejs/plugin-vue";
import { fileURLToPath, URL } from "node:url";

export default defineConfig({
    plugins: [vue()],
    test: {
        environment: "jsdom",
        globals: true,
        coverage: {
            provider: "v8",
            reporter: ["text", "html"],
            include: ["app.vue"],
            exclude: [
                "node_modules/**",
                ".nuxt/**",
                ".output/**",
            ],
        },
    },
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./", import.meta.url)),
        },
    },
});
