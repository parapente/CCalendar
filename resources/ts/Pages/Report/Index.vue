<script setup lang="ts">
import ReportList from "@/Components/Reports/ReportList.vue";
import CasUserLayout from "@/Layouts/CasUserLayout.vue";

const props = defineProps<{
    reports: Array<App.Models.Report>;
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
        <ReportList :reports="reports"></ReportList>
    </CasUserLayout>
</template>
