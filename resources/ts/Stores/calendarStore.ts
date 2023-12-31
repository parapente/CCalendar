import { defineStore } from "pinia";

export const useCalendarStore = defineStore("calendar", {
    state: (): {
        calendars: Array<App.Models.Calendar>;
        calendarEvents: Array<
            App.Models.CalendarEvent & { cas_user?: App.Models.CasUser }
        >;
    } => {
        return {
            calendars: [],
            calendarEvents: [],
        };
    },
});
