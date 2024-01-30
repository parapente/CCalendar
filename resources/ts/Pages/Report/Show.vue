<script setup lang="ts">
import Foldable from "@/Components/Foldable.vue";
import ReportData from "@/Components/Reports/ReportData.vue";
import ReportMissing from "@/Components/Reports/ReportMissing.vue";
import ReportView from "@/Components/Reports/ReportView.vue";
import CasUserLayout from "@/Layouts/CasUserLayout.vue";
import type { PageWithSharedProps } from "@/pageprops";
import { usePage } from "@inertiajs/vue3";

const props = defineProps<{
    report: App.Models.Report;
    data: Array<App.Models.ReportData & { cas_user: App.Models.CasUser }>;
    missing: Array<App.Models.CasUser>;
}>();

const page = usePage<PageWithSharedProps>();
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
        <div class="py-4 max-w-7xl mx-auto sm:px-6 lg:px-8 dark:text-white">
            <ReportView
                :report="report"
                v-if="page.props.cas_user_role !== 'Supervisor'"
            />
            <div v-else>
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
    </CasUserLayout>
</template>
