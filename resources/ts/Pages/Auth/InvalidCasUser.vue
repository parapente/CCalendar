<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import route from "ziggy";

const props = defineProps<{
    user: Record<string, string>;
}>();
</script>

<template>
    <div
        class="flex flex-col items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-white text-black dark:bg-gray-900 dark:text-gray-100"
    >
        <div class="flex flex-col text-center">
            <div class="text-2xl mb-16">
                Ο λογαριασμός σας με όνομα χρήστη {{ user.uid }} δεν έχει
                δικαίωμα πρόσβασης στο σύστημα.
            </div>
            <a
                :href="route('cas.logout')"
                class="text-2xl underline text-blue-600"
                >Πατήστε εδώ για αποσύνδεση</a
            >
        </div>
    </div>
</template>
