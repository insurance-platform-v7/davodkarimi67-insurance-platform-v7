import axios from "axios";
import { navigateTo, useCookie } from "#imports";

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

        if (status === 401) {
            await navigateTo("/login");
        } else if (status === 403) {
            await navigateTo("/403");
        } else if (status === 422) {
            console.error("API validation error", error);
        } else if (status === 500) {
            console.error("API server error", error);
        } else {
            console.error("API error", error);
        }

        return Promise.reject(error);
    },
);


