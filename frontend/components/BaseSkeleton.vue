<template>
    <div class="skeleton" aria-hidden="true"></div>
</template>

<style scoped>
.skeleton {
    min-height: 1rem;
    width: 100%;
    border-radius: 0.25rem;
    background: #e5e7eb;
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.6; }
    50% { opacity: 1; }
}
</style>
