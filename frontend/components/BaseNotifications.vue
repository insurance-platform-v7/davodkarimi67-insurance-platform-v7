<script setup lang="ts">
const { notifications, clear } = useNotification();
</script>
<template>
    <section v-if="notifications.length" aria-label="Notifications">
        <div v-for="(notification, index) in notifications" :key="index">
            {{ notification }}
        </div>
        <button type="button" @click="clear">Clear notifications</button>
    </section>
</template>
