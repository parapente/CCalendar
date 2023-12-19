<script setup lang="ts">
import Calendar from "@/Components/Calendar/Calendar.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useCalendarStore } from "@/Stores/calendarStore";

const props = defineProps<{
    calendars: Array<App.Models.Calendar>;
}>();

const store = useCalendarStore();
store.calendars = [...props.calendars];
</script>

<template>
    <AppLayout>
        <div class="flex flex-col items-center mx-2">
            <Calendar administrator></Calendar>
        </div>
    </AppLayout>
</template>
