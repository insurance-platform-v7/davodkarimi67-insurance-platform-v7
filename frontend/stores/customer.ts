import { defineStore } from "pinia";

export const useCustomerStore = defineStore("customer", {
    state: () => ({
        customers: [] as unknown[],
    }),
});
