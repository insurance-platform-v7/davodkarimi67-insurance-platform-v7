import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        currentUser: null as unknown,
        permissions: [] as string[],
        tenant: null as unknown,
        isAuthenticated: false,
    }),
});
