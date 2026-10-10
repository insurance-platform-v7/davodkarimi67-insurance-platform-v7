
<script setup lang="ts">
import { ref } from "vue";
const props = withDefaults(defineProps<{ accept?: string; maxSize?: number }>(), { accept: "*/*", maxSize: 5 * 1024 * 1024 });
const emit = defineEmits<{ change: [file: File]; invalid: [message: string] }>();
const dragging = ref(false);
const progress = ref(0);
const error = ref("");
const validate = (file: File) => {
error.value = "";
if (props.maxSize && file.size > props.maxSize) { error.value = "File is too large"; emit("invalid", error.value); return false; }
return true;
};
const selectFile = (file?: File) => {
if (!file || !validate(file)) return;
progress.value = 100;
emit("change", file);
};
const onInput = (event: Event) => selectFile((event.target as HTMLInputElement).files?.[0]);
const onDrop = (event: DragEvent) => { dragging.value = false; selectFile(event.dataTransfer?.files?.[0]); };
</script>
<template>
<div>
<label for="base-uploader">Upload file</label>
<input id="base-uploader" type="file" :accept="props.accept" @change="onInput" />
<div role="button" tabindex="0" :aria-label="dragging ? 'Drop file' : 'Drag and drop file'" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="onDrop">Drag & Drop</div>
<progress v-if="progress" :value="progress" max="100" aria-label="Upload progress">{{ progress }}%</progress>
<p v-if="error" role="alert">{{ error }}</p>
</div>
</template>
