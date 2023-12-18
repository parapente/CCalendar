<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import { faPen, faPlus } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link } from "@inertiajs/vue3";
import route from "ziggy";

const props = defineProps<{
    calendars: App.Models.Calendar[];
}>();
</script>

<template>
    <AppLayout>
        <div class="m-4 dark:text-white">
            <div class="text-xl mb-4">Ημερολόγια</div>
            <Link
                as="button"
                :href="route('administrator.calendar.create')"
                class="bg-blue-500 hover:bg-blue-300 text-black px-3 py-2 rounded-lg"
                ><FontAwesomeIcon :icon="faPlus" class="mr-1" />Δημιουργία
                νέου</Link
            >
            <ul>
                <li
                    v-for="calendar in calendars"
                    :key="calendar.id"
                    class="p-4 my-4 bg-slate-300 dark:bg-gray-400 text-black shadow-xl dark:shadow-md dark:shadow-gray-200 rounded-lg flex items-center"
                >
                    <div>{{ calendar.name }}</div>
                    <div
                        class="mx-2 p-4 border-black border"
                        :style="{ 'background-color': calendar.color }"
                    ></div>
                    <Link
                        as="button"
                        :href="
                            route('administrator.calendar.edit', calendar.id)
                        "
                        class="ml-auto px-3 py-2 bg-blue-500 hover:bg-blue-300 rounded-lg shadow-lg"
                        ><FontAwesomeIcon :icon="faPen"
                    /></Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
