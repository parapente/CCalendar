<script setup lang="ts">
import Calendar from "@/Components/Calendar/Calendar.vue";

const props = defineProps<{
    modelValue?: boolean;
}>();
</script>

<template>
    <Calendar></Calendar>
</template>
