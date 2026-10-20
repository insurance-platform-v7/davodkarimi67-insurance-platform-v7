export const useToast = () => {
    const success = (message: string) => console.log(`[SUCCESS] ${message}`);
    const error = (message: string) => console.error(`[ERROR] ${message}`);
    const validation = (message: string) => console.warn(`[VALIDATION] ${message}`);
    const business = (message: string) => console.warn(`[BUSINESS] ${message}`);
    const system = (message: string) => console.error(`[SYSTEM] ${message}`);
    return { success, error, validation, business, system };
};
