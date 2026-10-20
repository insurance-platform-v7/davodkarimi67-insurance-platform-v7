import { useAuthStore } from "~/stores/auth";

export default defineNuxtRouteMiddleware((to) => {
    const authStore = useAuthStore();
    const requiredPermission = to.meta.permission as string | undefined;

    if (requiredPermission && !authStore.permissions.includes(requiredPermission)) {
        return navigateTo("/403");
    }
});
