<template><section aria-labelledby="policy-widget-title"><h2 id="policy-widget-title">Policies</h2><strong>{{ value }}</strong></section></template>
<script setup lang="ts">
withDefaults(defineProps<{ value?: number }>(), { value: 0 });
</script>
