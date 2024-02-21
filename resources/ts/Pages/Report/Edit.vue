<script setup lang="ts">
import CasUserLayout from "@/Layouts/CasUserLayout.vue";
import ReportEdit from "@/Components/Reports/ReportEdit.vue";

const props = defineProps<{
    report: App.Models.Report;
    types: Array<Record<string, string>>;
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
        <ReportEdit :report="report" :types="types" />
    </CasUserLayout>
</template>
