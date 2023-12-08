import type { Ref } from "vue";

export type CalendarView = {
    year: Ref<number>;
    month: Ref<number>;
    day: Ref<number>;
};

export type CalendarDay = {
    date: string;
    isToday: boolean;
    isHoliday: boolean;
    holiday?: string;
    isDisabled: boolean;
};

export type CalendarEvent = {
    id: number;
    title: string;
    description: string;
    event_type: number;
    start_date: string;
    end_date: string;
    location: string;
    url: string;
};
