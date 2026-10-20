import js from "@eslint/js";
import vue from "eslint-plugin-vue";
import tseslint from "typescript-eslint";
import prettier from "eslint-config-prettier";

export default tseslint.config(
    {
        ignores: [
            "node_modules/**",
            "vendor/**",
            "storage/**",
            "bootstrap/cache/**",
            "public/build/**",
            "coverage-html/**",
        ],
    },

    js.configs.recommended,
    ...tseslint.configs.recommended,
    ...vue.configs["flat/recommended"],

    {
        files: [
            "resources/js/**/*.{js,jsx,ts,tsx}",
            "frontend/**/*.{js,jsx,ts,tsx}",
        ],
        languageOptions: {
            globals: {
                window: "readonly",
                document: "readonly",
                module: "readonly",
                require: "readonly",
                process: "readonly",
            },
        },
    },

    {
        files: ["**/*.cjs"],
        languageOptions: {
            globals: {
                module: "readonly",
                require: "readonly",
                process: "readonly",
            },
        },
    },

    prettier,
);
