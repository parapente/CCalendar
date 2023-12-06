<script setup lang="ts">
const props = defineProps<{
    dayOfWeek: number;
}>();
</script>

<template>
    <div
        class="w-full text-center border-black"
        :class="dayOfWeek != 6 ? 'border-r' : ''"
    >
        <slot />
    </div>
</template>
