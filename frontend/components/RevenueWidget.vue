<template><section aria-labelledby="revenue-widget-title"><h2 id="revenue-widget-title">Revenue</h2><strong>{{ value }}</strong></section></template>
<script setup lang="ts">
withDefaults(defineProps<{ value?: number }>(), { value: 0 });
</script>
