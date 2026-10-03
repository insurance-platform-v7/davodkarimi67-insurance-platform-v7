import { defineStore } from "pinia";

export const useFormulaStore = defineStore("formula", {
    state: () => ({
        formulas: [] as unknown[],
    }),
});
