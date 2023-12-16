<script setup lang="ts">
import type { PageWithSharedProps } from "@/pageprops";
import {
    faPencil,
    faUser,
    faUserNinja,
    faUserPlus,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link, usePage } from "@inertiajs/vue3";
import route from "ziggy";

const props = defineProps<{
    index: number;
    user: App.Models.User & { role: string };
    type: "admin" | "cas";
}>();

const page = usePage<PageWithSharedProps>();
</script>

<template>
    <div
        class="flex flex-wrap flex-row bg-white dark:bg-gray-800 dark:text-white mx-4 my-4 rounded-lg p-4 shadow-lg dark:shadow-md dark:shadow-gray-700 content-center"
    >
        <div class="py-2 my-auto">
            {{ index + 1 }}.
            <FontAwesomeIcon
                v-if="user.role === 'Administrator'"
                :icon="faUserNinja"
                size="lg"
                class="mx-1 text-red-500"
            />
            <FontAwesomeIcon
                v-if="user.role === 'Supervisor'"
                :icon="faUserPlus"
                size="lg"
                class="mx-1 text-blue-500"
            />
            <FontAwesomeIcon
                v-if="user.role === 'User'"
                :icon="faUser"
                size="lg"
                class="mx-1"
            />
            {{ user.name }}
        </div>
        <span class="text-blue-500 mx-1 py-2 grow my-auto"
            >({{ user.username }})</span
        >
        <Link
            :href="route('administrator.user.edit', [user.id, type])"
            class="transition ease-in-out duration-300 mx-1 hover:bg-sky-300 hover:shadow-xl hover:-translate-y-0.5 rounded-md px-3 py-2"
            ><FontAwesomeIcon :icon="faPencil"
        /></Link>
    </div>
</template>
