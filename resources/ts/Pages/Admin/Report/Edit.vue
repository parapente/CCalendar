<script setup lang="ts">
import ReportEdit from "@/Components/Reports/ReportEdit.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps<{
    report: App.Models.Report;
    types: Array<Record<string, string>>;
}>();
</script>

<template>
    <AppLayout title="Αναφορές">
        <template #header>
            <div
                class="font-semibold text-xl text-gray-800 dark:text-white leading-tight"
            >
                Αναφορές
            </div>
        </template>
        <ReportEdit :report="report" :types="types" />
    </AppLayout>
</template>
