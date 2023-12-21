<script setup lang="ts">
import EventCard from "./EventCard.vue";

const props = defineProps<{
    filteredCalendars: Array<App.Models.Calendar>;
    filteredCalendarEvents: Array<
        App.Models.CalendarEvent & { cas_user?: App.Models.CasUser }
    >;
}>();

const emit = defineEmits<{
    deleteEvent: [value: number];
    editEvent: [value: number];
}>();
</script>

<template>
    <div v-if="filteredCalendarEvents.length > 0" class="dark:text-white">
        <div class="text-xl text-bold mb-2">Εκδηλώσεις μήνα:</div>
        <div>
            <EventCard
                v-for="event in filteredCalendarEvents"
                :key="event.id"
                :event="event"
                @editEvent="emit('editEvent', event.id)"
                @deleteEvent="emit('deleteEvent', event.id)"
            />
        </div>
    </div>
</template>
