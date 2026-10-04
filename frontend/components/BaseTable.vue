<script setup lang="ts">
import { computed, ref } from "vue";

type Row = Record<string, unknown>;

const props = withDefaults(defineProps<{
    rows?: Row[];
    columns?: string[];
    pageSize?: number;
}>(), {
    rows: () => [],
    columns: () => [],
    pageSize: 10,
});

const page = ref(1);
const sortKey = ref("");
const sortDirection = ref<"asc" | "desc">("asc");
const filter = ref("");

const filteredRows = computed(() => {
    const query = filter.value.toLowerCase();
    return props.rows.filter((row) => {
        if (!query) return true;
        return Object.values(row).some((value) => String(value).toLowerCase().includes(query));
    });
});

const sortedRows = computed(() => {
    if (!sortKey.value) return filteredRows.value;
    return [...filteredRows.value].sort((a, b) => {
        const left = String(a[sortKey.value] ?? "");
        const right = String(b[sortKey.value] ?? "");
        const result = left.localeCompare(right);
        return sortDirection.value === "asc" ? result : -result;
    });
});

const paginatedRows = computed(() => {
    const start = (page.value - 1) * props.pageSize;
    return sortedRows.value.slice(start, start + props.pageSize);
});

const totalPages = computed(() => Math.max(1, Math.ceil(sortedRows.value.length / props.pageSize)));

const sortBy = (key: string) => {
    if (sortKey.value === key) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortKey.value = key;
        sortDirection.value = "asc";
    }
};

const exportCsv = () => {
    const headers = props.columns.join(",");
    const body = sortedRows.value.map((row) => props.columns.map((column) => JSON.stringify(row[column] ?? "")).join(",")).join("\\n");
    const blob = new Blob([`${headers}\\n${body}`], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = "export.csv";
    link.click();
    URL.revokeObjectURL(url);
};
</script>

<template>
    <div>
        <div>
            <input v-model="filter" type="search" placeholder="Filter" />
            <button type="button" @click="exportCsv">Export</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th v-for="column in columns" :key="column">
                        <button type="button" @click="sortBy(column)">{{ column }}</button>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row, index) in paginatedRows" :key="index">
                    <td v-for="column in columns" :key="column">{{ row[column] }}</td>
                </tr>
            </tbody>
        </table>

        <div>
            <button type="button" :disabled="page <= 1" @click="page--">Previous</button>
            <span>Page {{ page }} of {{ totalPages }}</span>
            <button type="button" :disabled="page >= totalPages" @click="page++">Next</button>
        </div>
    </div>
</template>
