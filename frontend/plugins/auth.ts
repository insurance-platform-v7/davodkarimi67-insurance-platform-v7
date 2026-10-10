import { useAuthStore } from "~/stores/auth";

export default defineNuxtPlugin(async () => {
    const token = useCookie<string | null>("access_token");

    if (!token.value) {
        return;
    }

    const authStore = useAuthStore();

    try {
        await authStore.fetchUser();
    } catch {
        authStore.currentUser = null;
        authStore.isAuthenticated = false;
    }
});
