import type { Ref } from "vue";

export type CalendarView = {
    year: Ref<number>;
    month: Ref<number>;
    day: Ref<number>;
};
