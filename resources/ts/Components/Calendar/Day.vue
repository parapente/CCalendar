<script setup lang="ts">
import { DateTime } from "luxon";
import type { CalendarDay } from "./types";
import { computed } from "vue";

const props = defineProps<{
    day: CalendarDay;
    calendars: Array<App.Models.Calendar>;
    calendarEvents: Array<App.Models.CalendarEvent>;
}>();

const dayNumber = computed(() => {
    return parseInt(
        DateTime.fromFormat(props.day.date, "yyyy-MM-dd").toFormat("dd")
    );
});

const bgColor = (event: App.Models.CalendarEvent) => {
    return props.calendars.find(
        (calendar) => calendar.id === event.calendar_id
    )!.color;
};

// Υπολόγισε το κατάλληλο χρώμα (λευκό ή μαύρο) ανάλογα με το χρώμα του φόντου
const fgColor = (event: App.Models.CalendarEvent) => {
    const bg = bgColor(event);

    const color = bg.charAt(0) === "#" ? bg.substring(1, 7) : bg;
    const r = parseInt(color.substring(0, 2), 16); // hexToR
    const g = parseInt(color.substring(2, 4), 16); // hexToG
    const b = parseInt(color.substring(4, 6), 16); // hexToB
    const ui_colors = [r / 255, g / 255, b / 255];
    const c = ui_colors.map((col) => {
        if (col <= 0.03928) {
            return col / 12.92;
        }
        return Math.pow((col + 0.055) / 1.055, 2.4);
    });
    const L = 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2];

    return L > 0.179 ? "#000000" : "#ffffff";
};
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
