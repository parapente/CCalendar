<script setup lang="ts">
import Foldable from "@/Components/Foldable.vue";
import ReportData from "@/Components/Reports/ReportData.vue";
import ReportMissing from "@/Components/Reports/ReportMissing.vue";
import ReportView from "@/Components/Reports/ReportView.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps<{
    report: App.Models.Report;
    data: Array<App.Models.ReportData & { cas_user: App.Models.CasUser }>;
    missing: Array<App.Models.CasUser>;
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
        <div class="py-4 max-w-7xl mx-auto sm:px-6 lg:px-8 dark:text-white">
            <div>
                <Foldable
                    :closed="true"
                    class="border border-black dark:border-white p-2 bg-amber-400 dark:bg-amber-700"
                >
                    <template #title> Προεπισκόπιση φόρμας</template>
                    <ReportView :report="report"></ReportView>
                </Foldable>
                <ReportMissing :missing="missing" />
                <ReportData :report="report" :data="data" class="mt-4" />
            </div>
        </div>
    </AppLayout>
</template>
