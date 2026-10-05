<script setup lang="ts">
import { ref, onMounted } from "vue";

const theme = ref<"light" | "dark">("light");

const setTheme = (value: "light" | "dark") => {
  theme.value = value;
  document.documentElement.dataset.theme = value;
};

onMounted(() => {
  const savedTheme = localStorage.getItem("theme");

  if (savedTheme === "dark" || savedTheme === "light") {
    setTheme(savedTheme);
  } else {
    setTheme("light");
  }
});

const toggleTheme = () => {
  const next = theme.value === "light" ? "dark" : "light";

  setTheme(next);
  localStorage.setItem("theme", next);
};
</script>

<template>
  <div>
    <button
      type="button"
      aria-label="Toggle theme"
      @click="toggleTheme"
    >
      Theme
    </button>
    <NuxtPage />
  </div>
</template>
