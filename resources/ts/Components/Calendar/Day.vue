<script setup lang="ts">
import { DateTime } from "luxon";
import type { CalendarDay } from "./types";
import { computed } from "vue";

const props = defineProps<{
    day: CalendarDay;
}>();

const dayNumber = computed(() => {
    return parseInt(
        DateTime.fromFormat(props.day.date, "yyyy-MM-dd").toFormat("dd")
    );
});
</script>

<template>
    <div
        class="cursor-default"
        :class="{
            'text-gray-500 bg-gray-300': props.day.isDisabled,
            'bg-purple-500': props.day.isHoliday,
            'bg-green-300': props.day.isToday,
        }"
        @click="!props.day.isDisabled ? $emit('triggered', dayNumber) : null"
    >
        <div>{{ dayNumber }}</div>
        <div class="text-xs text-ellipsis overflow-hidden">
            {{ props.day?.holiday }}
        </div>
    </div>
</template>
