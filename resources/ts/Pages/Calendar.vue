<script setup lang="ts">
import Calendar from "@/Components/Calendar/Calendar.vue";

const props = defineProps<{
    calendars: Array<App.Models.Calendar>;
}>();
</script>

<template>
    <div class="flex flex-col items-center justify-center">
        <Calendar :calendars="calendars"></Calendar>
    </div>
</template>
