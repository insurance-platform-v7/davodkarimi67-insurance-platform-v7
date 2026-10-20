import { api } from "./api";

export const policyService = {
    list: () => api.get("/policies"),
    get: (id: string) => api.get(`/policies/${id}`),
    issue: (data: unknown) => api.post("/policies/issue", data),
};
