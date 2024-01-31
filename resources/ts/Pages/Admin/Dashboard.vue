<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    faCalendarAlt,
    faEye,
    faFileLines,
    faUsers,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link } from "@inertiajs/vue3";
import route from "ziggy";
</script>

<template>
    <AppLayout title="Αρχική">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Αρχική σελίδα
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg dark:text-white p-4"
                >
                    <div>
                        Καλωσορίσατε στην εφαρμογή δικτυακών ημερολογίων.
                        Παρακαλώ επιλέξτε μια από τις παρακάτω λειτουργίες:
                    </div>
                    <div class="flex gap-4 my-4">
                        <Link
                            :href="route('administrator.user.index')"
                            class="border rounded-lg bg-sky-400 p-4 shadow-lg dark:shadow-black dark:border-black w-56"
                        >
                            <div class="flex flex-col text-center text-black">
                                <FontAwesomeIcon :icon="faUsers" size="5x" />
                                <div class="mt-3">Διαχείριση χρηστών</div>
                            </div>
                        </Link>
                        <Link
                            :href="route('administrator.calendar.index')"
                            class="border rounded-lg bg-sky-400 p-4 shadow-lg dark:shadow-black dark:border-black w-56"
                        >
                            <div class="flex flex-col text-center text-black">
                                <FontAwesomeIcon
                                    :icon="faCalendarAlt"
                                    size="5x"
                                />
                                <div class="mt-3">Διαχείριση ημερολογίων</div>
                            </div>
                        </Link>
                        <Link
                            :href="route('administrator.calendar.overview')"
                            class="border rounded-lg bg-sky-400 p-4 shadow-lg dark:shadow-black dark:border-black w-56"
                        >
                            <div class="flex flex-col text-center text-black">
                                <FontAwesomeIcon :icon="faEye" size="5x" />
                                <div class="mt-3">Εποπτεία ημερολογίων</div>
                            </div>
                        </Link>
                        <Link
                            :href="route('administrator.report.index')"
                            class="border rounded-lg bg-sky-400 p-4 shadow-lg dark:shadow-black dark:border-black w-56"
                        >
                            <div class="flex flex-col text-center text-black">
                                <FontAwesomeIcon
                                    :icon="faFileLines"
                                    size="5x"
                                />
                                <div class="mt-3">Αναφορές</div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
