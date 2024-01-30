<script setup lang="ts">
import type { PageWithSharedProps } from "@/pageprops";
import { usePage } from "@inertiajs/vue3";
import route from "ziggy";

const props = defineProps<{
    report: App.Models.Report;
    data: Array<App.Models.ReportData & { cas_user: App.Models.CasUser }>;
}>();

const page = usePage<PageWithSharedProps>();

const routePrefix = page.props.auth.user ? "administrator." : "";

const parseFilename = (data: string | null): Array<string> => {
    if (!data) return ["", ""];

    const result = JSON.parse(data);

    if (result.filename) {
        return [result.filename, result.real_filename];
    } else {
        return ["", ""];
    }
};

const getFileLink = (reportData: App.Models.ReportData): string => {
    return route(routePrefix + "report.getFile", {
        report: props.report,
        report_data: reportData,
    });
};
</script>

<template>
    <div>
        <div v-if="report.type === 1" class="flex flex-col">
            Δεδομένα αναφοράς:
            <table class="border-collapse mr-auto">
                <thead>
                    <tr>
                        <th>Όνομα</th>
                        <th>Αρχείο αναφοράς</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="line in data">
                        <td>{{ line.cas_user.name }}</td>
                        <td>
                            <a :href="getFileLink(line)">
                                {{ parseFilename(line.data)[1] }}
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <a
                :href="
                    route(routePrefix + 'report.getAllFiles', {
                        report: report,
                    })
                "
                class="mt-4 px-3 py-2 bg-blue-500 rounded-lg text-center mr-auto"
                >Λήψη όλων των αρχείων</a
            >
        </div>
        <div v-else>Άγνωστος τύπος αναφοράς!</div>
    </div>
</template>

<style type="text/css">
table,
table th,
table td {
    @apply border border-white p-2;
}
</style>
