<script setup lang="ts">
import Calendar from "@/Components/Calendar/Calendar.vue";
import { useCalendarStore } from "@/Stores/calendarStore";

const props = defineProps<{
    calendars: Array<App.Models.Calendar>;
}>();

const store = useCalendarStore();
store.calendars = [...props.calendars];
</script>

<template>
    <div class="flex flex-col items-center justify-center mx-2">
        <Calendar></Calendar>
    </div>
</template>
