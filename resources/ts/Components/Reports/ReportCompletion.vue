<script setup lang="ts">
const props = defineProps<{
    report: App.Models.Report;
}>();
</script>

<template>
    <div>{{ report.name }}</div>
</template>
