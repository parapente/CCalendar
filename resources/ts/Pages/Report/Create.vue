<script setup lang="ts">
import ReportCreation from "@/Components/Reports/ReportCreation.vue";
import CasUserLayout from "@/Layouts/CasUserLayout.vue";

const props = defineProps<{
    types: Array<Record<string, string>>;
}>();
</script>

<template>
    <CasUserLayout title="Αναφορές">
        <template #header>
            <div
                class="font-semibold text-xl text-gray-800 dark:text-white leading-tight"
            >
                Αναφορές
            </div>
        </template>
        <ReportCreation :types="types" />
    </CasUserLayout>
</template>
