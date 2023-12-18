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
import type { AxiosResponse } from "axios";
import CalendarLegend from "./CalendarLegend.vue";

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
    if (calendarEventVisible.value) {
        return;
    }

    const currentTime = DateTime.local();
    const start_date =
        DateTime.local(view.year.value, view.month.value, day)
            .startOf("minute")
            .plus({ hours: currentTime.hour, minutes: currentTime.minute })
            .toISO({
                includeOffset: false,
                suppressMilliseconds: true,
                suppressSeconds: true,
            }) ??
        DateTime.local().startOf("minute").toISO({
            includeOffset: false,
            suppressMilliseconds: true,
            suppressSeconds: true,
        });

    const end_date =
        DateTime.local(view.year.value, view.month.value, day)
            .startOf("minute")
            .plus({ hours: currentTime.hour + 1, minutes: currentTime.minute })
            .toISO({
                includeOffset: false,
                suppressMilliseconds: true,
                suppressSeconds: true,
            }) ??
        DateTime.local().startOf("minute").toISO({
            includeOffset: false,
            suppressMilliseconds: true,
            suppressSeconds: true,
        });

    newEvent.value.id = 0;
    newEvent.value.title = "";
    newEvent.value.description = "";
    newEvent.value.start_date = start_date;
    newEvent.value.end_date = end_date;
    newEvent.value.location = "";
    newEvent.value.url = "";

    calendarEventVisible.value = true;
};

const editCalendarEvent = (id: number) => {
    const event = calendarStore.calendarEvents.find((event) => event.id === id);

    if (event) {
        newEvent.value.id = event.id;
        newEvent.value.title = event.title;
        newEvent.value.description = event.description;
        newEvent.value.start_date =
            DateTime.fromSQL(event.start_date).startOf("minute").toISO({
                includeOffset: false,
                suppressMilliseconds: true,
                suppressSeconds: true,
            }) ?? "";
        newEvent.value.end_date =
            DateTime.fromSQL(event.end_date).startOf("minute").toISO({
                includeOffset: false,
                suppressMilliseconds: true,
                suppressSeconds: true,
            }) ?? "";
        newEvent.value.calendar_id = event.calendar_id;
        newEvent.value.location = event.location;
        newEvent.value.url = event.url;
        calendarEventVisible.value = true;
    }
};

const deleteCalendarEvent = (id: number) => {
    const event = calendarStore.calendarEvents.find((event) => event.id === id);
    console.log("Event: ", event);
    if (event) {
        axios
            .delete(`/calendar/${event.calendar_id}/event/${event.id}`)
            .then(
                (
                    response: AxiosResponse<{
                        success: boolean;
                        message: string;
                    }>
                ) => {
                    if (response.data.success) {
                        calendarStore.calendarEvents =
                            calendarStore.calendarEvents.filter(
                                (event) => event.id !== id
                            );
                    } else {
                        alert(response.data.message);
                    }
                }
            )
            .catch((error: AxiosError) => {
                if (error.request?.status === 419) {
                    // CSRF token has expired
                    window.location.reload();
                } else {
                    console.log(error);
                }
            });
    }
};

const saveCalendarEvent = (event: App.Models.CalendarEvent) => {
    console.log("Saving calendar event: ", event);
    axios
        .post(`/calendar/${event.calendar_id}/event`, event)
        .then((res) => {
            console.log(res.data);
        })
        .catch((error: AxiosError) => {
            if (error.request?.status === 419) {
                // CSRF token has expired
                window.location.reload();
            } else {
                console.log(error);
            }
        });

    getCalendarEventData(view.year.value, view.month.value);
    clearNewEvent();
};

const calendarEventVisible = ref(false);
const newEvent: Ref<App.Models.CalendarEvent> = ref({
    id: 0,
    title: "",
    description: "",
    start_date: DateTime.local().startOf("minute").toISO({
        includeOffset: false,
        suppressMilliseconds: true,
        suppressSeconds: true,
    }),
    end_date: DateTime.local().startOf("minute").plus({ hours: 1 }).toISO({
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
    <div
        class="m-2 border border-black text-black bg-white dark:border-gray-400 dark:text-gray-300 dark:bg-gray-600 rounded-t-lg max-w-screen-xl w-full"
    >
        <div class="px-1 flex text-center">
            <div class="w-full text-2xl font-bold">{{ view.year }}</div>
        </div>
        <div
            class="px-1 w-full flex justify-between text-center border-black dark:border-gray-400 border-y"
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
        <div class="grid grid-cols-7 dark:bg-gray-800">
            <DayName
                class="text-center overflow-hidden border-black dark:border-gray-400 dark:bg-gray-600 border-b"
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
                class="h-36 max-h-36 text-center overflow-hidden border-black dark:border-gray-400 border-b"
                :class="
                    index % 7 > 4
                        ? index % 7 === 6
                            ? 'bg-blue-300 dark:bg-blue-800'
                            : 'bg-blue-300 dark:bg-blue-800 border-r border-black dark:border-gray-400'
                        : 'border-r border-black dark:border-gray-400'
                "
                :calendarEvents="dayEvents(day)"
                @triggered="addCalendarEvent"
            ></Day>
        </div>
        <div class="flex justify-between"></div>
    </div>
    <div class="flex flex-row max-w-screen-xl w-full pt-4">
        <CalendarLegend />
        <div class="grow"></div>
    </div>
    <CalendarEventForm
        :event="newEvent"
        v-model:visible="calendarEventVisible"
        @save="saveCalendarEvent"
    />
    <CalendarEventList
        class="max-w-screen-xl w-full mt-4"
        @editEvent="editCalendarEvent"
        @deleteEvent="deleteCalendarEvent"
    />
</template>
