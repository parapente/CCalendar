<script setup lang="ts">
import ReportList from "@/Components/Reports/ReportList.vue";
import CasUserLayout from "@/Layouts/CasUserLayout.vue";
import type { PaginationProps } from "@/pagination";

const props = defineProps<{
    reports: PaginationProps<App.Models.Report>;
    answered: Array<number>;
}>();
</script>

<template>
    <CasUserLayout>
        <template #header>
            <div
                class="font-semibold text-xl text-gray-800 dark:text-white leading-tight"
            >
                Αναφορές
            </div>
        </template>
        <ReportList :reports="reports" :answered="answered" />
    </CasUserLayout>
</template>
