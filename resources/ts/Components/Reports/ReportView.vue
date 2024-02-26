<script setup lang="ts">
import type { PageWithSharedProps } from "@/pageprops";
import { useForm, usePage } from "@inertiajs/vue3";
import route from "ziggy";

const props = defineProps<{
    report: App.Models.Report;
    data?: Array<App.Models.ReportData>;
}>();

const forms = useForm<{
    file: File | null;
}>({
    file: null,
});

const page = usePage<PageWithSharedProps>();

const routePrefix = page.props.auth.user ? "administrator." : "";

const fileChanged = (e: Event) => {
    if (e.target !== null) {
        const fileInput = e.target as HTMLInputElement;
        forms.file = fileInput.files ? fileInput.files[0] : null;
    }
};

const onSubmit = () => {
    forms.post(
        route(routePrefix + "report.uploadReport", {
            id: props.report.id,
        })
    );
};

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
    <div class="flex flex-col gap-6">
        <div class="text-2xl underline font-bold">
            {{ report.name }}
        </div>
        <div>
            <a
                class="px-3 py-2 bg-orange-500 hover:bg-orange-700 rounded-lg"
                :href="
                    route(routePrefix + 'report.getCalendarToWord', report.id)
                "
                test-data-id="report-calendar-to-word"
            >
                Λήψη καταγραφών ημερολογίου
            </a>
        </div>
        <div
            v-if="props.data && props.data.length"
            class="flex flex-col bg-green-500 text-black p-2"
        >
            <div class="flex">
                <div class="mr-2">Έχετε ανεβάσει ήδη το αρχείο:</div>
                <a
                    :href="getFileLink(props.data[0])"
                    class="underline text-blue-600"
                    test-data-id="report-uploaded-file-link"
                    >{{ parseFilename(props.data[0].data)[1] }}</a
                >
            </div>
            <div>
                Αν επιθυμείτε μπορείτε να ανεβάσετε νέο αρχείο πραγματοποιώντας
                εκ νέου αποθήκευση.
            </div>
        </div>
        <div>
            <label for="file" class="mr-4">Αρχείο τριμηνιαίας αναφοράς:</label>
            <input type="file" name="file" @input="fileChanged" accept=".pdf" />
            <div v-if="forms.errors.file" class="text-red-500 text-sm">
                {{ forms.errors.file }}
            </div>
        </div>
        <div>
            <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                @click.prevent="onSubmit"
                test-data-id="report-upload-button"
            >
                Αποθήκευση
            </button>
        </div>
    </div>
</template>
