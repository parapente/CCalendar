<script setup lang="ts">
import { DateTime } from "luxon";
import type { CalendarDay } from "./types";
import { computed } from "vue";
import { useCalendarStore } from "@/Stores/calendarStore";
import { bgColor, fgColor } from "./utilities";

const props = defineProps<{
    day: CalendarDay;
    calendarEvents: Array<App.Models.CalendarEvent>;
}>();

const calendarStore = useCalendarStore();

const dayNumber = computed(() => {
    return parseInt(
        DateTime.fromFormat(props.day.date, "yyyy-MM-dd").toFormat("dd")
    );
});
</script>

<template>
    <div
        class="cursor-default"
        :class="{
            'text-gray-500 bg-gray-300': props.day.isDisabled,
            'bg-purple-500': props.day.isHoliday,
            'bg-green-300': props.day.isToday,
        }"
        @click="!props.day.isDisabled ? $emit('triggered', dayNumber) : null"
    >
        <div>{{ dayNumber }}</div>
        <div class="text-xs text-ellipsis overflow-hidden">
            {{ props.day?.holiday }}
        </div>
        <div
            v-for="event in calendarEvents"
            class="text-sm overflow-hidden whitespace-nowrap border m-1 text-ellipsis px-1"
            :style="{
                'background-color': `${bgColor(event)}`,
                color: `${fgColor(event)}`,
            }"
        >
            {{ event.title }}
        </div>
    </div>
</template>
