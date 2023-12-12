<script setup lang="ts">
const props = defineProps<{
    calendarEvents: App.Models.CalendarEvent[];
}>();
</script>

<template>
    <div v-if="props.calendarEvents.length > 0">
        <h1>Εκδηλώσεις μήνα:</h1>
        <ul>
            <li v-for="event in calendarEvents" :key="event.id">
                {{ event.title }}
            </li>
        </ul>
    </div>
</template>
