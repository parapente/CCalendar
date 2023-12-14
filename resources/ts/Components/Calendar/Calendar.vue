<script setup lang="ts">
import { computed, ref, watch, onMounted, type Ref } from "vue";
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
import { useCalendarStore } from "@/Stores/calendarStore";
import { daysOfMonth, daysOfWeek } from "./utilities";

const calendarStore = useCalendarStore();

const date = new Date();

const view: CalendarView = {
    year: ref(date.getFullYear()),
    month: ref(date.getMonth() + 1),
    day: ref(date.getDate()),
};

const monthName = computed(() => Info.months("long")[view.month.value - 1]);

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
    const date =
        DateTime.local(view.year.value, view.month.value, day).toISO({
            includeOffset: false,
            suppressMilliseconds: true,
            suppressSeconds: true,
        }) ??
        DateTime.local().toISO({
            includeOffset: false,
            suppressMilliseconds: true,
            suppressSeconds: true,
        });

    console.log("Date: ", date);
    newEvent.value.start_date = date;

    if (!calendarEventVisible.value) {
        newEvent.value.title = "";
        newEvent.value.description = "";
    }

    calendarEventVisible.value = true;
};

const saveCalendarEvent = (event: App.Models.CalendarEvent) => {
    axios
        .post(`/calendar/${event.calendar_id}/event`, event)
        .then((res) => {
            console.log(res.data);
        })
        .catch((err: AxiosError) => {
            console.log(err);
        });

    getCalendarEventData(view.year.value, view.month.value);
    clearNewEvent();
};

const calendarEventVisible = ref(false);
const newEvent: Ref<App.Models.CalendarEvent> = ref({
    id: 0,
    title: "",
    description: "",
    start_date: DateTime.local().toISO({
        includeOffset: false,
        suppressMilliseconds: true,
        suppressSeconds: true,
    }),
    end_date: DateTime.local().plus({ hours: 1 }).toISO({
        includeOffset: false,
        suppressMilliseconds: true,
        suppressSeconds: true,
    }),
    location: "",
    url: "",
    calendar_id: 0,
    created_at: null,
    updated_at: null,
});

const holidays = ref(greekHolidays(`${view.year.value}`));

const clearNewEvent = () => {
    newEvent.value = {
        id: 0,
        title: "",
        description: "",
        start_date: DateTime.local().toISO({
            includeOffset: false,
            suppressMilliseconds: true,
            suppressSeconds: true,
        }),
        end_date: DateTime.local().plus({ hours: 1 }).toISO({
            includeOffset: false,
            suppressMilliseconds: true,
            suppressSeconds: true,
        }),
        location: "",
        url: "",
        calendar_id: 0,
        created_at: null,
        updated_at: null,
    };
};

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

    calendarStore.calendarEvents = [...new_events];
};

const dayEvents = (day: CalendarDay) => {
    return calendarStore.calendarEvents.filter((event) => {
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
                v-for="(day, index) in daysOfMonth(
                    view.year.value,
                    view.month.value,
                    holidays
                )"
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
                @triggered="addCalendarEvent"
            ></Day>
        </div>
        <div class="flex justify-between"></div>
    </div>
    <CalendarEventForm
        :event="newEvent"
        v-model:visible="calendarEventVisible"
        @save="saveCalendarEvent"
    />
    <CalendarEventList class="max-w-screen-xl w-full mt-4" />
</template>
