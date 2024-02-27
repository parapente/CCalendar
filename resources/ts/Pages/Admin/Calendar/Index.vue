<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    faCheckCircle,
    faPen,
    faPlus,
    faXmarkCircle,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link, router } from "@inertiajs/vue3";
import route from "ziggy";

const props = defineProps<{
    calendars: App.Models.Calendar[];
}>();

const toggleActiveForCalendar = (calendar_id: number) => {
    router.post(
        route("administrator.calendar.toggleActive", {
            id: calendar_id,
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
    <AppLayout title="Ημερολόγια">
        <template #header>
            <div
                class="font-semibold text-xl text-gray-800 dark:text-white leading-tight"
            >
                Ημερολόγια
            </div>
        </template>
        <div class="py-4 max-w-7xl mx-auto sm:px-6 lg:px-8 dark:text-white">
            <div class="m-4 py-2">
                <Link
                    as="button"
                    :href="route('administrator.calendar.create')"
                    class="bg-blue-500 hover:bg-blue-300 text-black px-4 py-3 rounded-md shadow-lg"
                    test-data-id="create-calendar-button"
                    ><FontAwesomeIcon :icon="faPlus" class="mr-1" />Δημιουργία
                    νέου ημερολογίου
                </Link>
            </div>
            <ul class="m-4">
                <li
                    v-for="calendar in calendars"
                    :key="calendar.id"
                    class="p-4 my-4 bg-slate-300 dark:bg-gray-800 text-black dark:text-white shadow-xl dark:shadow-md dark:shadow-gray-700 rounded-lg flex items-center"
                >
                    <div>{{ calendar.name }}</div>
                    <div
                        class="mx-2 p-4 border-black border"
                        :style="{ 'background-color': calendar.color }"
                    ></div>
                    <button
                        as="button"
                        class="ml-auto mr-4 px-3 py-2 rounded-lg"
                        @click="toggleActiveForCalendar(calendar.id)"
                        :test-data-id="`toggle-calendar-${calendar.id}-button`"
                    >
                        <FontAwesomeIcon
                            class="text-green-500"
                            v-if="calendar.active"
                            :icon="faCheckCircle"
                        /><FontAwesomeIcon
                            class="text-red-500"
                            v-if="!calendar.active"
                            :icon="faXmarkCircle"
                        />
                    </button>
                    <Link
                        as="button"
                        :href="
                            route('administrator.calendar.edit', calendar.id)
                        "
                        class="px-3 py-2 bg-blue-500 hover:bg-blue-300 rounded-lg shadow-lg"
                        :test-data-id="`edit-calendar-${calendar.id}-button`"
                        ><FontAwesomeIcon :icon="faPen"
                    /></Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
