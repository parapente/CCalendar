<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        type: "normal" | "success" | "danger" | "warning";
    }>(),
    {
        type: "normal",
    }
);
</script>

<template>
    <div
        class="p-4 rounded-md shadow-lg"
        :class="
            props.type === 'normal'
                ? 'bg-gray-400 border-gray-500 text-gray-800 border-2'
                : props.type === 'success'
                ? 'bg-green-300 border-green-400 text-green-800 border-2'
                : props.type === 'danger'
                ? 'bg-red-300 border-red-400 text-red-800 border-2'
                : props.type === 'warning'
                ? 'bg-orange-300 border-orange-400 text-orange-800 border-2'
                : ''
        "
    >
        <slot />
    </div>
</template>
