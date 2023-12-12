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
