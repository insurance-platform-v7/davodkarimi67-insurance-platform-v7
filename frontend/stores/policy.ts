import { defineStore } from "pinia";

export const usePolicyStore = defineStore("policy", {
    state: () => ({
        policies: [] as unknown[],
    }),
});
