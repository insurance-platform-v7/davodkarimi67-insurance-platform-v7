<script setup lang="ts">
import { ref, onMounted } from "vue";

const theme = ref("light");

const setTheme = (value: string) => {
  theme.value = value;
  document.documentElement.dataset.theme = value;
};

onMounted(() => setTheme(localStorage.getItem("theme") ?? "light"));

const toggleTheme = () => {
  const next = theme.value === "light" ? "dark" : "light";
  setTheme(next);
  localStorage.setItem("theme", next);
};
</script>

<template>
  <div>
    <button type="button" aria-label="Toggle theme" @click="toggleTheme">
      Theme
    </button>
    <NuxtPage />
  </div>
</template>