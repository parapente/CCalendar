<script setup lang="ts">
import ReportView from "@/Components/Reports/ReportView.vue";
import CasUserLayout from "@/Layouts/CasUserLayout.vue";

const props = defineProps<{
    report: App.Models.Report;
}>();
</script>

<template>
    <CasUserLayout title="Αναφορές">
        <template #header>
            <div
                class="font-semibold text-xl text-gray-800 dark:text-white leading-tight"
            >
                Αναφορές
            </div>
        </template>
        <ReportView :report="report"></ReportView>
    </CasUserLayout>
</template>
