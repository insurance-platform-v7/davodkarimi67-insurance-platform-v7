import { api } from "./api";

export const formulaService = {
    list: () => api.get("/formulas"),
    get: (id: string) => api.get(`/formulas/${id}`),
    execute: (data: unknown) => api.post("/formulas/execute", data),
};
