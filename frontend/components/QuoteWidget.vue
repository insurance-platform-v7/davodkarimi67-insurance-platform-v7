<template><section aria-labelledby="quote-widget-title"><h2 id="quote-widget-title">Quotes</h2><strong>{{ value }}</strong></section></template>
<script setup lang="ts">
withDefaults(defineProps<{ value?: number }>(), { value: 0 });
</script>
