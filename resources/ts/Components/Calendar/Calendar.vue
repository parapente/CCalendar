<script setup lang="ts">
import { computed, ref } from "vue";
import type { CalendarView } from "./types";
import { DateTime, Info } from "luxon";
import DayName from "./DayName.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faCalendar,
    faCalendarAlt,
    faArrowLeft,
    faArrowRight,
} from "@fortawesome/free-solid-svg-icons";
import Day from "./Day.vue";

const props = defineProps<{
    modelValue?: boolean;
}>();

const date = new Date();

const view: CalendarView = {
    year: ref(date.getFullYear()),
    month: ref(date.getMonth() + 1),
    day: ref(date.getDate()),
};

const monthName = computed(() => Info.months("long")[view.month.value - 1]);
const daysOfMonth = () => {
    const days = [];
    const firstDay = DateTime.local(
        view.year.value,
        view.month.value,
        1
    ).startOf("week").day;
    const endOfPreviousMonth = DateTime.local(
        view.year.value,
        view.month.value - 1,
        1
    ).endOf("month").day;
    const endOfCurrentMonth = DateTime.local(
        view.year.value,
        view.month.value,
        1
    ).endOf("month").day;

    // Ο προηγούμενος μήνας εμφανίζεται πριν την πρώτη
    // ημέρα αν το firstDay δεν είναι η 1η του τρέχοντος μήνα
    let previousMonthDisplayed = firstDay === 1;
    if (!previousMonthDisplayed) {
        for (let i = firstDay; i <= endOfPreviousMonth; i++) {
            days.push({
                day: i,
                disabled: true,
            });
        }
        previousMonthDisplayed = true;
    }
    for (let i = 1; i <= endOfCurrentMonth; i++) {
        days.push({
            day: i,
            disabled: false,
        });
    }

    if (days.length % 7) {
        const missingDays = 7 - (days.length % 7);

        for (let i = 1; i <= missingDays; i++) {
            days.push({
                day: i,
                disabled: true,
            });
        }
    }

    return days;
};

const daysToRows = () => {
    const rows = [];
    const days = daysOfMonth();

    for (let i = 0; i < days.length; i += 7) {
        rows.push(days.slice(i, i + 7));
    }

    return rows;
};

const months = Info.months();
const daysOfWeek = Info.weekdays("short");
console.log(daysOfMonth);
</script>

<template>
    <div class="m-1 border border-black rounded-t-lg">
        <div class="px-1 w-full flex justify-between">
            <button><FontAwesomeIcon :icon="faArrowLeft" /></button>
            <div>{{ view.year }}</div>
            <button><FontAwesomeIcon :icon="faArrowRight" /></button>
        </div>
        <div class="w-full text-center border-black border-y">
            <FontAwesomeIcon class="px-1" :icon="faCalendarAlt" />Ημερολόγιο
            {{ monthName }}
        </div>
        <div class="flex border-black border-b">
            <DayName
                class="w-1/6"
                :day-of-week="index"
                v-for="(day, index) in daysOfWeek"
                :key="day"
                >{{ day }}</DayName
            >
        </div>
        <div
            v-for="(row, rowIndex) in daysToRows()"
            class="w-full flex border-b border-black"
        >
            <Day
                v-for="(day, index) in row"
                :day="day"
                class="w-1/6 h-16 text-center"
                :class="
                    index > 4
                        ? index === 6
                            ? 'bg-blue-300'
                            : 'bg-blue-300 border-r border-black'
                        : 'border-r border-black'
                "
            ></Day>
        </div>
        <div class="flex justify-between"></div>
    </div>
</template>
