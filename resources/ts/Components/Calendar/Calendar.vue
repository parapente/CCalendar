<script setup lang="ts">
import { computed, ref, watch, type Ref, onMounted } from "vue";
import type { CalendarDay, CalendarView } from "./types";
import { DateTime, Info } from "luxon";
import DayName from "./DayName.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faCalendarAlt,
    faArrowLeft,
    faArrowRight,
} from "@fortawesome/free-solid-svg-icons";
import Day from "./Day.vue";
import CalendarEventForm from "./CalendarEventForm.vue";
import { greekHolidays } from "greek-holidays";
import CalendarEventList from "./CalendarEventList.vue";
import axios from "axios";

const date = new Date();

const view: CalendarView = {
    year: ref(date.getFullYear()),
    month: ref(date.getMonth() + 1),
    day: ref(date.getDate()),
};

const monthName = computed(() => Info.months("long")[view.month.value - 1]);
const daysOfMonth = () => {
    const days: CalendarDay[] = [];
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
                date: DateTime.local(
                    view.year.value,
                    view.month.value - 1,
                    i
                ).toFormat("yyyy-MM-dd"),
                isDisabled: true,
                isHoliday: false,
                isToday:
                    DateTime.local(
                        view.year.value,
                        view.month.value - 1,
                        i
                    ).toFormat("yyyy-MM-dd") ===
                    DateTime.local().toFormat("yyyy-MM-dd"),
            });
        }
        previousMonthDisplayed = true;
    }
    for (let i = 1; i <= endOfCurrentMonth; i++) {
        const dateString = DateTime.local(
            view.year.value,
            view.month.value,
            i
        ).toFormat("yyyy-MM-dd");
        const holidayIndex = holidays.value
            .map((holiday) => holiday.date)
            .indexOf(dateString);
        days.push({
            date: dateString,
            isDisabled: false,
            isHoliday: holidayIndex !== -1,
            holiday:
                holidayIndex !== -1 ? holidays.value[holidayIndex].name : "",
            isToday: dateString === DateTime.local().toFormat("yyyy-MM-dd"),
        });
    }

    if (days.length % 7) {
        const missingDays = 7 - (days.length % 7);

        for (let i = 1; i <= missingDays; i++) {
            const dateString = DateTime.local(
                view.month.value === 12 ? view.year.value + 1 : view.year.value,
                view.month.value === 12 ? 1 : view.month.value + 1,
                i
            ).toFormat("yyyy-MM-dd");
            days.push({
                date: dateString,
                isDisabled: true,
                isHoliday: false,
                isToday: dateString === DateTime.local().toFormat("yyyy-MM-dd"),
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

const prevMonth = () => {
    view.month.value--;
    if (view.month.value < 1) {
        view.month.value = 12;
        holidays.value = greekHolidays(`${view.year.value - 1}`);
        view.year.value--;
    }
};

const nextMonth = () => {
    view.month.value++;
    if (view.month.value > 12) {
        view.month.value = 1;
        holidays.value = greekHolidays(`${view.year.value + 1}`);
        view.year.value++;
    }
};

const addCalendarEvent = (day: number) => {
    newEventDate.value =
        DateTime.local(view.year.value, view.month.value, day).toISODate() ??
        DateTime.local().toISODate();
    calendarEventVisible.value = true;
};

const saveCalendarEvent = (event: App.Models.CalendarEvent) => {
    events.value.push(event);
};

const calendarEventVisible = ref(false);
const newEventDate = ref(DateTime.local().toISODate());
const holidays = ref(greekHolidays(`${view.year.value}`));

const getCalendarEvents = computed(() => {
    const new_events: App.Models.CalendarEvent[] = [];
    axios
        .get(`/events/${view.year.value}/${view.month.value}`)
        .then((response) => {
            if (
                response.status === 200 &&
                Array.isArray(response.data) &&
                response.data.length
            ) {
                response.data.forEach((event: App.Models.CalendarEvent) => {
                    new_events.push(event);
                });
            } else {
                console.log(`Error: ${response.status}`);
            }
        });

    return new_events;
});

const events: Ref<App.Models.CalendarEvent[]> = getCalendarEvents;
</script>

<template>
    <div class="m-1 border border-black rounded-t-lg">
        <div class="px-1 flex text-center">
            <div class="w-full text-2xl font-bold">{{ view.year }}</div>
        </div>
        <div
            class="px-1 w-full flex justify-between text-center border-black border-y"
        >
            <button @click="prevMonth" type="button">
                <FontAwesomeIcon :icon="faArrowLeft" size="xl" />
            </button>
            <div class="text-lg font-bold py-2">
                <FontAwesomeIcon class="px-1" :icon="faCalendarAlt" />Ημερολόγιο
                {{ monthName }}
            </div>
            <button @click="nextMonth" type="button">
                <FontAwesomeIcon :icon="faArrowRight" size="xl" />
            </button>
        </div>
        <div class="flex border-black border-b">
            <DayName
                class="w-[14.3%] text-center"
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
                class="w-[14.3%] h-16 text-center max-w-[14.3%] overflow-hidden"
                :class="
                    index > 4
                        ? index === 6
                            ? 'bg-blue-300'
                            : 'bg-blue-300 border-r border-black'
                        : 'border-r border-black'
                "
                @triggered="addCalendarEvent"
            ></Day>
        </div>
        <div class="flex justify-between"></div>
    </div>
    <CalendarEventForm
        :eventDate="newEventDate"
        v-model:visible="calendarEventVisible"
        @save="saveCalendarEvent"
    />
    <CalendarEventList :calendarEvents="events" />
</template>
