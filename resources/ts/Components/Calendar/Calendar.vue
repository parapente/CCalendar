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
import type { AxiosError } from "axios";

const props = defineProps<{
    calendars: Array<App.Models.Calendar>;
}>();

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

const getCalendarEventData = async (year: number, month: number) => {
    let new_events: App.Models.CalendarEvent[] = [];
    await axios
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
            }
        })
        .catch((error: AxiosError) => {
            console.error(error.toJSON());
        });

    events.value = [...new_events];
};

const events: Ref<App.Models.CalendarEvent[]> = ref([]);

const dayEvents = (day: CalendarDay) => {
    return events.value.filter((event) => {
        const start_date = DateTime.fromSQL(event.start_date).toISODate();
        const end_date = DateTime.fromSQL(event.end_date).toISODate();
        const day_date = DateTime.fromSQL(day.date).toISODate();

        if (start_date === null || end_date === null || day_date === null) {
            console.log("Invalid SQL date for event: " + event);
            return false;
        } else {
            return start_date <= day_date && end_date >= day_date;
        }
    });
};

watch([view.year, view.month], ([newYear, newMonth]) => {
    getCalendarEventData(newYear, newMonth);
});

onMounted(() => getCalendarEventData(view.year.value, view.month.value));
</script>

<template>
    <div class="m-1 border border-black rounded-t-lg max-w-screen-xl w-full">
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
        <div class="grid grid-cols-7">
            <DayName
                class="text-center overflow-hidden border-black border-b"
                :day-of-week="index"
                v-for="(day, index) in daysOfWeek"
                :key="day"
                >{{ day }}</DayName
            >
            <Day
                v-for="(day, index) in daysOfMonth()"
                :day="day"
                class="h-36 max-h-36 text-center overflow-hidden border-black border-b"
                :class="
                    index % 7 > 4
                        ? index % 7 === 6
                            ? 'bg-blue-300'
                            : 'bg-blue-300 border-r border-black'
                        : 'border-r border-black'
                "
                :calendarEvents="dayEvents(day)"
                :calendars="calendars"
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
    <CalendarEventList
        class="max-w-screen-xl w-full mt-4"
        :calendarEvents="events"
    />
</template>
