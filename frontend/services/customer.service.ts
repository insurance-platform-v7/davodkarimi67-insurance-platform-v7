import { api } from "./api";

export const customerService = {
    list: () => api.get("/customers"),
    get: (id: string) => api.get(`/customers/${id}`),
    create: (data: unknown) => api.post("/customers", data),
};
