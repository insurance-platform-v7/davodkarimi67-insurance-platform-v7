import { defineStore } from "pinia";
import { navigateTo, useCookie } from "#imports";
import { api } from "~/services/api";

interface LoginCredentials {
    email: string;
    password: string;
}

interface AuthUser {
    id: number;
    [key: string]: unknown;
}

export const useAuthStore = defineStore("auth", {
    state: () => ({
        currentUser: null as AuthUser | null,
        permissions: [] as string[],
        tenant: null as unknown,
        isAuthenticated: false,
    }),

    actions: {
        async login(credentials: LoginCredentials) {
            const response = await api.post("/auth/login", credentials);
            const token = response.data?.token;

            if (token) {
                useCookie<string | null>("access_token").value = token;
            }

            await this.fetchUser();
        },

        async fetchUser() {
            const response = await api.get("/auth/me");

            this.currentUser = response.data?.user ?? null;
            this.isAuthenticated = !!this.currentUser;
        },

        async logout() {
            try {
                await api.post("/auth/logout");
            } finally {
                useCookie<string | null>("access_token").value = null;
                this.currentUser = null;
                this.permissions = [];
                this.tenant = null;
                this.isAuthenticated = false;

                await navigateTo("/login");
            }
        },
    },
});
