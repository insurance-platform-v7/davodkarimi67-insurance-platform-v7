import { defineConfig } from "vitest/config";
import vue from "@vitejs/plugin-vue";
import { fileURLToPath, URL } from "node:url";

export default defineConfig({
    plugins: [vue()],

   test: {
    environment: "jsdom",
    globals: true,
    include: ["tests/**/*.{test,spec}.ts"],
    exclude: ["tests/e2e/**", "node_modules/**", ".output/**", ".nuxt/**"],
    coverage: {
        provider: "v8",
        reporter: ["text", "html"],
        include: ["app.vue"],
        exclude: ["node_modules/**", ".output/**", ".nuxt/**"],
    },
},

    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./", import.meta.url)),
            "~": fileURLToPath(new URL("./", import.meta.url)),
            "#imports": fileURLToPath(
                new URL("./tests/mocks/nuxt-imports.ts", import.meta.url),
            ),
        },
    },
});
