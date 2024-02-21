<script setup lang="ts">
import Modal from "@/Components/Modal.vue";
import {
    faArrowRight,
    faCheckCircle,
    faPen,
    faPlus,
    faTrashCan,
    faXmarkCircle,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link, router } from "@inertiajs/vue3";
import { ref, type Ref } from "vue";
import route from "ziggy";
import { usePage } from "@inertiajs/vue3";
import type { PageWithSharedProps } from "@/pageprops";
import type { PaginationProps } from "@/pagination";
import Pagination from "../Pagination.vue";

const props = defineProps<{
    reports: PaginationProps<App.Models.Report>;
    answered: Array<number>;
}>();

const page = usePage<PageWithSharedProps>();

const showModal = ref(false);

const selectedForDeletion: Ref<App.Models.Report | undefined> = ref();

const routePrefix = page.props.auth.user ? "administrator." : "";

const deleteReport = (report: App.Models.Report) => {
    selectedForDeletion.value = report;
    showModal.value = true;
};

const deletionApproved = () => {
    router.delete(
        route("administrator.report.destroy", selectedForDeletion.value?.id),
        {
            onFinish: () => {
                showModal.value = false;
            },
        }
    );
};

const toggleActiveForReport = (report_id: number) => {
    router.post(
        route(routePrefix + "report.toggleActive", {
            id: report_id,
        }),
        {},
        {
            onSuccess: () => {
                router.reload({
                    only: ["reports"],
                    preserveScroll: true,
                });
            },
        }
    );
};
</script>

<template>
    <div>
        <div class="py-4 max-w-7xl mx-auto sm:px-6 lg:px-8 dark:text-white">
            <div class="m-4 py-2">
                <Link
                    v-if="
                        page.props.auth.user ||
                        page.props.cas_user_role === 'Supervisor'
                    "
                    as="button"
                    :href="route(routePrefix + 'report.create')"
                    class="bg-blue-500 hover:bg-blue-300 text-black px-4 py-3 rounded-md shadow-lg"
                    test-data-id="create-report-button"
                    ><FontAwesomeIcon :icon="faPlus" class="mr-1" />Δημιουργία
                    νέας αναφοράς
                </Link>
            </div>
            <div v-if="reports.data.length">
                <ul class="m-4 py-2">
                    <li
                        v-for="(report, index) in reports.data"
                        :key="report.id"
                        class="my-4 bg-slate-300 dark:bg-gray-800 text-black dark:text-white shadow-xl dark:shadow-md dark:shadow-gray-700 rounded-lg flex flex-col"
                        :test-data-id="`report-${report.id}`"
                    >
                        <div class="p-4 flex items-center">
                            <div class="mr-auto">
                                {{ index + reports.from }}. {{ report.name }}
                            </div>
                            <button
                                v-if="
                                    page.props.auth.user ||
                                    page.props.cas_user_role === 'Supervisor'
                                "
                                class="mr-4 px-3 py-2 rounded-lg"
                                @click="toggleActiveForReport(report.id)"
                                test-data-id="toggle-active-button"
                            >
                                <FontAwesomeIcon
                                    class="text-green-500"
                                    v-if="report.active"
                                    :icon="faCheckCircle"
                                />
                                <FontAwesomeIcon
                                    class="text-red-500"
                                    v-if="!report.active"
                                    :icon="faXmarkCircle"
                                />
                            </button>
                            <Link
                                v-if="
                                    page.props.auth.user ||
                                    page.props.cas_user_role === 'Supervisor'
                                "
                                as="button"
                                :href="
                                    route(
                                        routePrefix + 'report.edit',
                                        report.id
                                    )
                                "
                                class="px-3 py-2 bg-blue-500 hover:bg-blue-300 rounded-lg shadow-lg mr-4"
                                test-data-id="edit-report-button"
                            >
                                <FontAwesomeIcon :icon="faPen" />
                            </Link>
                            <Link
                                as="button"
                                :href="
                                    route(
                                        routePrefix + 'report.show',
                                        report.id
                                    )
                                "
                                class="px-3 py-2 bg-orange-500 hover:bg-orange-300 rounded-lg shadow-lg mr-4"
                                test-data-id="show-report-button"
                            >
                                <FontAwesomeIcon :icon="faArrowRight" />
                            </Link>
                            <button
                                v-if="!page.props.cas_user"
                                class="px-3 py-2 bg-red-500 hover:bg-red-300 rounded-lg shadow-lg mr-4"
                                @click="deleteReport(report)"
                                test-data-id="delete-report-button"
                            >
                                <FontAwesomeIcon :icon="faTrashCan" />
                            </button>
                        </div>
                        <div
                            class="px-2 flex rounded-b-lg text-black"
                            :class="
                                answered && answered.includes(report.id)
                                    ? 'bg-green-500 dark:bg-green-500'
                                    : 'bg-gray-400'
                            "
                            test-data-id="report-status"
                        >
                            {{
                                answered && answered.includes(report.id)
                                    ? "Υπάρχουν απαντήσεις"
                                    : "Σε αναμονή απαντήσεων"
                            }}
                        </div>
                    </li>
                </ul>
                <Pagination
                    :from="reports.from"
                    :to="reports.to"
                    :total="reports.total"
                    :links="reports.links"
                    v-if="reports.next_page_url !== reports.prev_page_url"
                />
            </div>
            <div v-else>Δεν υπάρχουν δημιουργημένες αναφορες</div>
        </div>
        <Modal :show="showModal">
            <div class="flex flex-col p-4 dark:text-white">
                <div>
                    Είστε σίγουροι ότι θέλετε να διαγράψετε την αναφορά "{{
                        selectedForDeletion?.name
                    }}";
                </div>
                <div class="flex justify-between mt-4">
                    <button
                        @click="showModal = false"
                        class="px-3 py-2 bg-gray-500 hover:bg-gray-300 rounded-lg shadow-lg"
                    >
                        Όχι
                    </button>
                    <button
                        @click="deletionApproved"
                        class="px-3 py-2 bg-red-500 hover:bg-red-300 rounded-lg shadow-lg"
                    >
                        Ναι
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>
