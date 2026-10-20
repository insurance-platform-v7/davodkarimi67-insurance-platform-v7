import { api } from "./api";

export const quoteService = {
  list: () => api.get("/quotes"),
  get: (id: string) => api.get(`/quotes/${id}`),
  create: (data: unknown) => api.post("/quotes", data),
  premium: (data: unknown) => api.post("/quotes/premium", data),
};
