<script setup lang="ts">
const props = defineProps<{
    missing: Array<App.Models.CasUser>;
}>();
</script>

<template>
    <div class="mt-4">
        <div v-if="missing.length" class="text-red-700 dark:text-orange-500">
            <div>Οι παρακάτω χρήστες δεν έχουν συμπληρώσει την αναφορά:</div>
            <ul class="list-disc list-inside">
                <li v-for="item in missing" :key="item.id">
                    {{ item.name }}
                </li>
            </ul>
        </div>
        <div v-else class="text-green-500">
            Όλοι οι χρήστες έχουν συμπληρώσει την αναφορά
        </div>
    </div>
</template>
