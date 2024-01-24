<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import route from "ziggy";

const props = defineProps<{
    report: App.Models.Report;
}>();

const forms = useForm<{
    file: File | null;
}>({
    file: null,
});

const fileChanged = (e: Event) => {
    if (e.target !== null) {
        const fileInput = e.target as HTMLInputElement;
        forms.file = fileInput.files ? fileInput.files[0] : null;
    }
};

const onSubmit = () => {
    forms.post(
        route("administrator.report.uploadReport", {
            id: props.report.id,
        })
    );
};
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
            <div class="flex flex-col gap-6">
                <div class="text-2xl underline font-bold">
                    {{ report.name }}
                </div>
                <div>
                    <a
                        class="px-3 py-2 bg-orange-500 hover:bg-orange-700 rounded-lg"
                        :href="
                            route(
                                'administrator.report.getCalendarToWord',
                                report.id
                            )
                        "
                    >
                        Λήψη καταγραφών ημερολογίου
                    </a>
                </div>
                <div>
                    <label for="file" class="mr-4"
                        >Αρχείο τριμηνιαίας αναφοράς:</label
                    >
                    <input type="file" name="file" @input="fileChanged" />
                    <div v-if="forms.errors.file" class="text-red-500 text-sm">
                        {{ forms.errors.file }}
                    </div>
                </div>
                <div>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        @click.prevent="onSubmit"
                    >
                        Αποθήκευση
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
