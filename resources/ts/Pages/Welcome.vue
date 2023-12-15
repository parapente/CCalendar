<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import route from "ziggy";
</script>

<template>
    <Head title="Welcome" />

    <div
        class="flex flex-col items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-white text-black dark:bg-gray-900 dark:text-gray-100"
    >
        <div class="flex flex-col text-center">
            <div class="text-4xl mb-16">
                Καλωσορίσατε στην εφαρμογή δικτυακών ημερολογίων
            </div>
            <a
                :href="route('calendar.index')"
                class="text-blue-500 my-4 py-2 px-4 rounded-md bg-blue-900 hover:bg-blue-600 hover:text-blue-300"
            >
                Είσοδος
            </a>
        </div>
    </div>
</template>

<style></style>
