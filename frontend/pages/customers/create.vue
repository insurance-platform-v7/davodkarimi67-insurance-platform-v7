<script setup lang="ts">
import { ref } from "vue";
import { useForm, useField } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from "zod";
import { useToast } from "~/composables/useToast";
import BaseSkeleton from "~/components/BaseSkeleton.vue";

const toast = useToast();
const loading = ref(false);

const schema = z.object({
    name: z.string().min(1, "Name is required"),
});

const { handleSubmit } = useForm({
    validationSchema: toTypedSchema(schema),
});
const { value: name, errorMessage } = useField<string>("name");

const onSubmit = handleSubmit(async (values) => {
    loading.value = true;
    try {
        console.log(values);
        toast.success("Customer created successfully");
    } finally {
        loading.value = false;
    }
});
</script>
<template>
    <div>
        <h1>Create Customer</h1>
        <BaseSkeleton v-if="loading" />
        <form v-else @submit="onSubmit">
            <label for="name">Name</label>
            <input id="name" v-model="name" name="name" />
            <span v-if="errorMessage">{{ errorMessage }}</span>
            <button type="submit">Create</button>
        </form>
    </div>
</template>
