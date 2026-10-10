<script setup lang="ts">
import { ref } from "vue";
import { useAuthStore } from "~/stores/auth";
import BaseSkeleton from "~/components/BaseSkeleton.vue";

const authStore = useAuthStore();
const email = ref("");
const password = ref("");
const loading = ref(false);

const submit = async () => {
    loading.value = true;
    try {
        await authStore.login({ email: email.value, password: password.value });
        await navigateTo("/dashboard");
    } finally {
        loading.value = false;
    }
};
</script>
<template>
    <form @submit.prevent="submit">
        <h1>Login</h1>
        <BaseSkeleton v-if="loading" />
        <template v-else>
            <input v-model="email" type="email" placeholder="Email" />
            <input v-model="password" type="password" placeholder="Password" />
            <button type="submit">Login</button>
        </template>
    </form>
</template>
