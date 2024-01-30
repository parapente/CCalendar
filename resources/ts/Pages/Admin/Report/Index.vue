<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import ReportList from "@/Components/Reports/ReportList.vue";
import type { PaginationProps } from "@/pagination";

const props = defineProps<{
    reports: PaginationProps<App.Models.Report>;
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
        <ReportList :reports="reports"></ReportList>
    </AppLayout>
</template>
