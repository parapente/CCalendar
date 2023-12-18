<script setup lang="ts">
import { useCalendarStore } from "@/Stores/calendarStore";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSquareFull } from "@fortawesome/free-solid-svg-icons";

const props = defineProps<{
    modelValue?: boolean;
}>();

const calendarStore = useCalendarStore();
</script>

<template>
    <div class="border-black dark:border-white border p-2">
        <div class="pb-1 underline font-bold dark:text-white">Ημερολόγια:</div>
        <div
            v-for="calendar in calendarStore.calendars"
            class="flex justify-between items-center dark:text-white"
        >
            <div>{{ calendar.name }}</div>
            <FontAwesomeIcon
                :icon="faSquareFull"
                class="mx-2"
                :style="{ color: calendar.color }"
            />
        </div>
    </div>
</template>
