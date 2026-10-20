import axios from "axios";
import { navigateTo, useCookie } from "#imports";
import { useToast } from "~/composables/useToast";

export const api = axios.create({
    baseURL: "/api/v1",
});

api.interceptors.request.use((config) => {
    const token = useCookie<string | null>("access_token").value;

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    config.headers["X-Correlation-ID"] = crypto.randomUUID();

    return config;
});

api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const status = error.response?.status;
        const toast = useToast();

        if (status === 401) {
            await navigateTo("/login");
            toast.system("Authentication required");
        } else if (status === 403) {
            await navigateTo("/403");
            toast.business("You do not have permission for this action");
        } else if (status === 422) {
            toast.validation("Please check the submitted data");
        } else {
            toast.system("An unexpected system error occurred");
        }

        return Promise.reject(error);
    },
);
