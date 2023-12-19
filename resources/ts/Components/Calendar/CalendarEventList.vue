<script setup lang="ts">
import { useCalendarStore } from "@/Stores/calendarStore";
import {
    faCalendarDay,
    faGlobe,
    faMapLocation,
    faPencil,
    faTrashCan,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { DateTime } from "luxon";
import { bgColor, fgColor } from "./utilities";
import DialogModal from "@/Components/DialogModal.vue";
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import type { PageWithSharedProps } from "@/pageprops";

const calendarStore = useCalendarStore();
const emit = defineEmits<{
    deleteEvent: [value: number];
    editEvent: [value: number];
}>();

const showDeleteDialog = ref(false);
const eventForDeletion = ref(0);

const confirmDeletion = (id: number) => {
    showDeleteDialog.value = true;
    eventForDeletion.value = id;
};

const proceedWithDeletion = () => {
    showDeleteDialog.value = false;
    emit("deleteEvent", eventForDeletion.value);
};

const page = usePage<PageWithSharedProps>();

const showUser = computed(() => {
    if (page.props.cas_user_role === "Supervisor" || page.props.auth.user) {
        return true;
    }

    return false;
});
</script>

<template>
    <div
        v-if="calendarStore.filteredCalendarEvents.length > 0"
        class="dark:text-white"
    >
        <DialogModal :show="showDeleteDialog" @close="showDeleteDialog = false">
            <template #title> Επιβεβαίωση διαγραφής εκδήλωσης </template>
            <template #content>
                <p>Είστε σίγουροι ότι θέλετε να διαγράψετε την εκδήλωση;</p>
            </template>
            <template #footer>
                <div class="flex w-full">
                    <button
                        @click="showDeleteDialog = false"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-auto"
                    >
                        Όχι
                    </button>
                    <button
                        @click="proceedWithDeletion"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Ναι
                    </button>
                </div>
            </template>
        </DialogModal>

        <div class="text-xl text-bold mb-2">Εκδηλώσεις μήνα:</div>
        <ul>
            <li
                v-for="event in calendarStore.filteredCalendarEvents"
                :key="event.id"
                class="border-black border mb-2 p-2 dark:border-white dark:bg-gray-700"
            >
                <div
                    class="flex flex-row items-center px-2"
                    :style="{
                        'background-color': `${bgColor(event)}`,
                        color: `${fgColor(event)}`,
                    }"
                >
                    <FontAwesomeIcon :icon="faCalendarDay" />
                    <div class="flex flex-col">
                        <div class="pl-2 text-lg">
                            {{
                                DateTime.fromSQL(
                                    event.start_date
                                ).toLocaleString(DateTime.DATETIME_MED)
                            }}
                            -
                            {{
                                DateTime.fromSQL(event.end_date).toLocaleString(
                                    DateTime.DATETIME_MED
                                )
                            }}
                        </div>
                        <div class="pl-2 text-sm">
                            ({{
                                calendarStore.calendars.find(
                                    (calendar) =>
                                        calendar.id === event.calendar_id
                                )?.name
                            }})
                        </div>
                    </div>
                    <div class="flex grow justify-end">
                        <button
                            class="my-1 px-3 py-2 rounded-lg bg-blue-500 shadow-md shadow-black hover:bg-blue-400 text-black"
                            @click="emit('editEvent', event.id)"
                        >
                            <FontAwesomeIcon :icon="faPencil" />
                        </button>
                        <button
                            class="ml-2 my-1 px-3 py-2 rounded-lg bg-red-500 shadow-md shadow-black hover:bg-red-400 text-black"
                            @click="confirmDeletion(event.id)"
                        >
                            <FontAwesomeIcon :icon="faTrashCan" />
                        </button>
                    </div>
                </div>
                <div class="flex">
                    <div class="text-lg font-bold mr-2" v-if="showUser">
                        {{ event.cas_user?.name }}:
                    </div>
                    <u class="text-lg font-bold">{{ event.title }}</u>
                </div>
                <div class="text">
                    {{ event.description }}
                </div>
                <div
                    class="flex flex-row items-center mt-2"
                    v-if="event.location"
                >
                    <FontAwesomeIcon :icon="faMapLocation" />
                    <div class="pl-1">
                        {{ event.location }}
                    </div>
                </div>
                <div class="flex flex-row items-center" v-if="event.url">
                    <FontAwesomeIcon :icon="faGlobe" />
                    <a
                        :href="event.url"
                        class="pl-1 underline"
                        target="_blank"
                        >{{ event.url }}</a
                    >
                </div>
            </li>
        </ul>
    </div>
</template>
