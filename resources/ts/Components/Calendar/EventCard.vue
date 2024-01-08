<script setup lang="ts">
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
import { useCalendarStore } from "@/Stores/calendarStore";
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import type { PageWithSharedProps } from "@/pageprops";

const props = defineProps<{
    event: App.Models.CalendarEvent & { cas_user?: App.Models.CasUser };
}>();

const calendarStore = useCalendarStore();

const emit = defineEmits<{
    deleteEvent: [value: number];
    editEvent: [value: number];
}>();

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
        class="border-black border mb-2 p-2 dark:border-white dark:bg-gray-700 break-inside-avoid print:bg-white"
    >
        <div
            class="flex flex-row items-center px-2 event_title"
            :style="{
                'background-color': `${bgColor(event)}`,
                color: `${fgColor(event)}`,
            }"
        >
            <FontAwesomeIcon :icon="faCalendarDay" />
            <div class="flex flex-col">
                <div class="pl-2 text-lg">
                    {{
                        DateTime.fromSQL(event.start_date).toLocaleString(
                            DateTime.DATETIME_MED
                        )
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
                            (calendar) => calendar.id === event.calendar_id
                        )?.name
                    }})
                </div>
            </div>
            <div class="flex grow justify-end">
                <button
                    class="my-1 px-3 py-2 rounded-lg bg-blue-500 shadow-md shadow-black hover:bg-blue-400 text-black print:hidden"
                    @click="emit('editEvent', event.id)"
                    v-if="
                        page.props.cas_user &&
                        page.props.cas_user.id === event.cas_user_id
                    "
                >
                    <FontAwesomeIcon :icon="faPencil" />
                </button>
                <button
                    class="ml-2 my-1 px-3 py-2 rounded-lg bg-red-500 shadow-md shadow-black hover:bg-red-400 text-black print:hidden"
                    @click="emit('deleteEvent', event.id)"
                    v-if="
                        page.props.cas_user &&
                        page.props.cas_user.id === event.cas_user_id
                    "
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
        <div class="flex flex-row items-center mt-2" v-if="event.location">
            <FontAwesomeIcon :icon="faMapLocation" />
            <div class="pl-1">
                {{ event.location }}
            </div>
        </div>
        <div class="flex flex-row items-center" v-if="event.url">
            <FontAwesomeIcon :icon="faGlobe" />
            <a :href="event.url" class="pl-1 underline" target="_blank">{{
                event.url
            }}</a>
        </div>
    </div>
</template>
<style type="text/css">
@media print {
    .event_title {
        color: black !important;
        background-color: white !important;
        border: 1px solid black !important;
        border-radius: 1em;
    }
}
</style>
