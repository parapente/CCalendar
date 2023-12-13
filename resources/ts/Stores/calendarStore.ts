import { defineStore } from "pinia";

export const useCalendarStore = defineStore("calendar", {
    state: (): {
        calendars: Array<App.Models.Calendar>;
        calendarEvents: Array<App.Models.CalendarEvent>;
    } => {
        return {
            calendars: [],
            calendarEvents: [],
        };
    },
});
