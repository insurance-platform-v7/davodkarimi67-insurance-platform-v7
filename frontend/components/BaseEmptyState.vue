<template>
    <div class="empty-state" role="status">
        <p>{{ message }}</p>
    </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{ message?: string }>(), {
    message: "No records found",
});
</script>

<style scoped>
.empty-state {
    padding: 2rem;
    text-align: center;
}
</style>
