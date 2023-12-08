<script setup lang="ts">
import type { CalendarEvent } from "./types";

const props = defineProps<{
    events: CalendarEvent[];
}>();
</script>

<template>
    <div v-if="events.length">
        <h1>Εκδηλώσεις μήνα:</h1>
        <ul>
            <li v-for="event in events" :key="event.id">
                {{ event.title }}
            </li>
        </ul>
    </div>
</template>
