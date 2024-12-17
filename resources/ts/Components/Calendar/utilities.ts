import { DateTime, Info } from "luxon";
import type { CalendarDay } from "./types";
import { greekHolidays } from "greek-holidays";
import { useCalendarStore } from "@/Stores/calendarStore";

export const daysOfMonth = (
    year: number,
    month: number,
    holidays: ReturnType<typeof greekHolidays>
) => {
    console.log("daysOfMonth", year, month);
    const days: CalendarDay[] = [];
    const firstDay = DateTime.local(year, month, 1).startOf("week").day;
    const endOfPreviousMonth = DateTime.local(
        year,
        month - 1 !== 0 ? month - 1 : 12,
        1
    ).endOf("month").day;
    const endOfCurrentMonth = DateTime.local(year, month, 1).endOf("month").day;

    // Ο προηγούμενος μήνας εμφανίζεται πριν την πρώτη
    // ημέρα αν το firstDay δεν είναι η 1η του τρέχοντος μήνα
    let previousMonthDisplayed = firstDay === 1;
    console.log(previousMonthDisplayed);
    if (!previousMonthDisplayed) {
        for (let i = firstDay; i <= endOfPreviousMonth; i++) {
            days.push({
                date: DateTime.local(
                    year,
                    month - 1 !== 0 ? month - 1 : 12,
                    i
                ).toFormat("yyyy-MM-dd"),
                isDisabled: true,
                isHoliday: false,
                isToday:
                    DateTime.local(
                        year,
                        month - 1 !== 0 ? month - 1 : 12,
                        i
                    ).toFormat("yyyy-MM-dd") ===
                    DateTime.local().toFormat("yyyy-MM-dd"),
            });
        }
        console.log(firstDay);
        previousMonthDisplayed = true;
    }
    for (let i = 1; i <= endOfCurrentMonth; i++) {
        const dateString = DateTime.local(year, month, i).toFormat(
            "yyyy-MM-dd"
        );
        const holidayIndex = holidays
            .map((holiday) => holiday.date)
            .indexOf(dateString);
        days.push({
            date: dateString,
            isDisabled: false,
            isHoliday: holidayIndex !== -1,
            holiday: holidayIndex !== -1 ? holidays[holidayIndex].name : "",
            isToday: dateString === DateTime.local().toFormat("yyyy-MM-dd"),
        });
    }

    if (days.length % 7) {
        const missingDays = 7 - (days.length % 7);

        for (let i = 1; i <= missingDays; i++) {
            const dateString = DateTime.local(
                month === 12 ? year + 1 : year,
                month === 12 ? 1 : month + 1,
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

export const daysOfWeek = Info.weekdays("short");

export const bgColor = (event: App.Models.CalendarEvent) => {
    const calendarStore = useCalendarStore();

    return calendarStore.calendars.find(
        (calendar) => calendar.id === event.calendar_id
    )!.color;
};

// Υπολόγισε το κατάλληλο χρώμα (λευκό ή μαύρο) ανάλογα με το χρώμα του φόντου
export const fgColor = (event: App.Models.CalendarEvent) => {
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
