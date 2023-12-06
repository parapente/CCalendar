<script setup lang="ts">
const props = defineProps<{
    day: {
        day: number;
        disabled: boolean;
    };
}>();
</script>

<template>
    <div :class="props.day.disabled ? 'text-gray-500 bg-gray-300' : ''">
        {{ props.day.day }}
    </div>
</template>
