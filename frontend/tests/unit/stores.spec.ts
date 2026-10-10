import { createPinia, setActivePinia } from "pinia";
import { describe, expect, it, beforeEach } from "vitest";
import { useAuthStore } from "~/stores/auth";
import { useCustomerStore } from "~/stores/customer";
import { useQuoteStore } from "~/stores/quote";
import { usePolicyStore } from "~/stores/policy";
import { useFormulaStore } from "~/stores/formula";

describe("frontend module stores", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });

    it("initializes auth store", () => {
        const store = useAuthStore();

        expect(store.currentUser).toBeNull();
        expect(store.permissions).toEqual([]);
        expect(store.tenant).toBeNull();
        expect(store.isAuthenticated).toBe(false);
    });

    it("initializes customer store", () => {
        const store = useCustomerStore();

        expect(store).toBeDefined();
    });

    it("initializes quote store", () => {
        const store = useQuoteStore();

        expect(store.quotes).toEqual([]);
    });

    it("initializes policy store", () => {
        const store = usePolicyStore();

        expect(store).toBeDefined();
    });

    it("initializes formula store", () => {
        const store = useFormulaStore();

        expect(store.formulas).toEqual([]);
    });
});