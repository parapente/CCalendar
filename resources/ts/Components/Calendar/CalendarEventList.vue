<script setup lang="ts">
import {
    faCalendarDay,
    faGlobe,
    faMapLocation,
    faPencil,
    faTrashCan,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { DateTime, Info } from "luxon";

const props = defineProps<{
    calendarEvents: App.Models.CalendarEvent[];
}>();
</script>

<template>
    <div v-if="props.calendarEvents.length > 0">
        <div class="text-xl text-bold mb-2">Εκδηλώσεις μήνα:</div>
        <ul>
            <li
                v-for="event in calendarEvents"
                :key="event.id"
                class="border-black border mb-2 p-2"
            >
                <div class="flex flex-row items-center px-2 bg-slate-300">
                    <FontAwesomeIcon :icon="faCalendarDay" />
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
                    <div class="flex grow justify-end">
                        <button class="my-1 px-3 py-2 rounded-lg bg-blue-500">
                            <FontAwesomeIcon :icon="faPencil" />
                        </button>
                        <button
                            class="ml-2 my-1 px-3 py-2 rounded-lg bg-red-500"
                        >
                            <FontAwesomeIcon :icon="faTrashCan" />
                        </button>
                    </div>
                </div>
                <div>
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
