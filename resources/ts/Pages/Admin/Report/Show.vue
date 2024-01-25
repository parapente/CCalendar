<script setup lang="ts">
import ReportView from "@/Components/Reports/ReportView.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps<{
    report: App.Models.Report;
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
        <ReportView :report="report"></ReportView>
    </AppLayout>
</template>
