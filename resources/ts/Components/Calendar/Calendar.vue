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
    faPlus,
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
import CalendarFilters from "./CalendarFilters.vue";
import { usePage } from "@inertiajs/vue3";
import type { PageWithSharedProps } from "@/pageprops";
import { useToast } from "vue-toast-notification";
import "vue-toast-notification/dist/theme-default.css";
import route from "ziggy";
import ConfirmDeleteDialog from "./ConfirmDeleteDialog.vue";
import EventModal from "./EventModal.vue";
import { vIntersectionObserver } from "@vueuse/components";

const props = withDefaults(
    defineProps<{
        administrator?: boolean;
    }>(),
    {
        administrator: false,
    }
);

const routePrefix = computed(() => {
    if (props.administrator) {
        return "administrator.";
    } else {
        return "";
    }
});

const calendarStore = useCalendarStore();

const toast = useToast();

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
            .delete(
                route("calendar.deleteEvent", [event.calendar_id, event.id])
            )
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
                        toast.success(response.data.message, {
                            position: "top-right",
                        });
                    } else {
                        toast.error(response.data.message, {
                            position: "top-right",
                        });
                    }
                }
            )
            .catch((error: AxiosError) => {
                console.log(error);
                toast.error(error.message, { position: "top-right" });
            });
    }
};

const saveCalendarEvent = (event: App.Models.CalendarEvent) => {
    axios
        .post(route("calendar.addEvent", event.calendar_id), event)
        .then((res) => {
            if (res.data.success) {
                getCalendarEventData(view.year.value, view.month.value);
                clearNewEvent();
                toast.success(res.data.message, { position: "top-right" });
            } else {
                toast.error(res.data.message, { position: "top-right" });
            }
        })
        .catch((error: AxiosError) => {
            console.log(error);
            toast.error(error.message, { position: "top-right" });
        });
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
    calendar_id: calendarStore.calendars.map((calendar) => calendar.id)[0],
    cas_user_id: 0,
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
        calendar_id: calendarStore.calendars.map((calendar) => calendar.id)[0],
        cas_user_id: 0,
        created_at: null,
        updated_at: null,
    };
};

const getCalendarEventData = async (year: number, month: number) => {
    let new_events: App.Models.CalendarEvent[] = [];
    await axios
        .get(
            route(`${routePrefix.value}events`, [
                view.year.value,
                view.month.value,
            ])
        )
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
            toast.error(error.message, { position: "top-right" });
        });

    calendarStore.calendarEvents = [...new_events];
};

const dayEvents = (day: CalendarDay) => {
    return filteredCalendarEvents.value.filter((event) => {
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

const page = usePage<PageWithSharedProps>();

const calendarFilter = ref("0");
const userFilter = ref("0");

const filteredCalendars = computed(() => {
    if (calendarFilter.value === "0") {
        return calendarStore.calendars;
    } else {
        return calendarStore.calendars.filter(
            (calendar) => calendar.id === parseInt(calendarFilter.value)
        );
    }
});

const filteredCalendarEvents = computed(() =>
    calendarStore.calendarEvents
        .filter(
            (event) =>
                calendarFilter.value === "0" ||
                event.calendar_id === parseInt(calendarFilter.value)
        )
        .filter(
            (event) =>
                userFilter.value === "0" ||
                event.cas_user_id === parseInt(userFilter.value)
        )
);

const showDeleteDialog = ref(false);
const eventForDeletion = ref(0);

const onDeleteEvent = (event: number) => {
    showEventModal.value = false;
    eventForDeletion.value = event;
    showDeleteDialog.value = true;
};

const proceedWithDeletion = (answer: "yes" | "no") => {
    showDeleteDialog.value = false;
    if (answer === "yes") {
        deleteCalendarEvent(eventForDeletion.value);
    }
};

const eventForModal: Ref<
    (App.Models.CalendarEvent & { cas_user?: App.Models.CasUser }) | null
> = ref(null);

const showEventModal = ref(false);

const onCalendarEventClicked = (id: number) => {
    eventForModal.value = calendarStore.calendarEvents.find(
        (event) => event.id === id
    )!;

    showEventModal.value = true;
};

const onEditEventModal = (id: number) => {
    showEventModal.value = false;
    editCalendarEvent(id);
};

const print = () => {
    window.print();
};

const eventListVisible = ref(false);

const onIntersectionObserver = ([
    { isIntersecting },
]: IntersectionObserverEntry[]) => {
    eventListVisible.value = isIntersecting;
};

watch([view.year, view.month], ([newYear, newMonth]) => {
    getCalendarEventData(newYear, newMonth);
});

onMounted(() => {
    getCalendarEventData(view.year.value, view.month.value);
});
</script>

<template>
    <ConfirmDeleteDialog
        :show="showDeleteDialog"
        @answer="proceedWithDeletion"
    ></ConfirmDeleteDialog>

    <EventModal
        :event="eventForModal"
        v-model:show="showEventModal"
        @editEvent="onEditEventModal"
        @deleteEvent="onDeleteEvent"
    ></EventModal>

    <button
        class="px-3 py-2 mt-2 bg-slate-400 hover:bg-slate-600 rounded-md dark:text-white print:hidden"
        @click="print"
    >
        Εκτύπωση
    </button>
    <CalendarFilters
        v-if="administrator || page.props.cas_user_role === 'Supervisor'"
        v-model:calendar="calendarFilter"
        v-model:user="userFilter"
        class="print:hidden"
    />
    <div
        class="m-2 border border-black text-black bg-white dark:border-gray-400 dark:text-gray-300 dark:bg-gray-600 rounded-t-lg max-w-screen-xl w-full print:hidden"
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
                @eventClicked="onCalendarEventClicked"
            ></Day>
        </div>
        <div class="flex justify-between"></div>
    </div>
    <div class="flex flex-row max-w-screen-xl w-full pt-4 print:hidden">
        <CalendarLegend />
    </div>
    <CalendarEventForm
        :event="newEvent"
        v-model:visible="calendarEventVisible"
        @save="saveCalendarEvent"
    />
    <Transition name="fade">
        <div
            class="fixed bottom-0 right-5 px-4 py-3 mb-5 bg-red-500 hover:bg-red-700 rounded-full shadow-lg shadow-black cursor-pointer print:hidden"
            @click="addCalendarEvent(DateTime.local().day)"
            v-if="!eventListVisible"
        >
            <FontAwesomeIcon :icon="faPlus" />
        </div>
    </Transition>
    <div class="hidden print:block">
        Ημερολόγιο {{ monthName }} {{ view.year.value }}
    </div>
    <CalendarEventList
        :filteredCalendars="filteredCalendars"
        :filteredCalendarEvents="filteredCalendarEvents"
        class="max-w-screen-xl w-full mt-4"
        @editEvent="editCalendarEvent"
        @deleteEvent="onDeleteEvent"
        v-intersection-observer="onIntersectionObserver"
    />
</template>
<style type="css">
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
