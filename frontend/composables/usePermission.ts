import { useAuthStore } from "~/stores/auth";

export const usePermission = () => {
    const authStore = useAuthStore();

    const can = (permission: string) => {
        return authStore.permissions.includes(permission);
    };

    return { can };
};
