import { ref } from "vue";

const cookies = new Map<string, ReturnType<typeof ref>>();

export function useCookie<T = string | null>(name: string) {
    if (!cookies.has(name)) {
        cookies.set(name, ref<T | null>(null));
    }

    return cookies.get(name)!;
}

export async function navigateTo(_path: string) {
    return undefined;
}