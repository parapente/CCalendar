import { DateTime, Info } from "luxon";
import type { CalendarDay } from "./types";
import { greekHolidays } from "greek-holidays";

export const daysOfMonth = (
    year: number,
    month: number,
    holidays: ReturnType<typeof greekHolidays>
) => {
    const days: CalendarDay[] = [];
    const firstDay = DateTime.local(year, month, 1).startOf("week").day;
    const endOfPreviousMonth = DateTime.local(year, month - 1, 1).endOf(
        "month"
    ).day;
    const endOfCurrentMonth = DateTime.local(year, month, 1).endOf("month").day;

    // Ο προηγούμενος μήνας εμφανίζεται πριν την πρώτη
    // ημέρα αν το firstDay δεν είναι η 1η του τρέχοντος μήνα
    let previousMonthDisplayed = firstDay === 1;
    if (!previousMonthDisplayed) {
        for (let i = firstDay; i <= endOfPreviousMonth; i++) {
            days.push({
                date: DateTime.local(year, month - 1, i).toFormat("yyyy-MM-dd"),
                isDisabled: true,
                isHoliday: false,
                isToday:
                    DateTime.local(year, month - 1, i).toFormat(
                        "yyyy-MM-dd"
                    ) === DateTime.local().toFormat("yyyy-MM-dd"),
            });
        }
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
