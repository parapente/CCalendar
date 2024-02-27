<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import UserCard from "@/Components/User/UserCard.vue";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link } from "@inertiajs/vue3";
import { TransitionGroup } from "vue";
import route from "ziggy";

const props = defineProps<{
    users: (App.Models.User & { role: string })[];
    casUsers: (App.Models.CasUser & { role: string })[];
}>();
</script>
<template>
    <AppLayout title="Χρήστες">
        <template #header>
            <div
                class="font-semibold text-xl text-gray-800 dark:text-white leading-tight"
            >
                Διαχείριση Χρηστών Εφαρμογής
            </div>
        </template>

        <div class="py-4 max-w-7xl mx-auto sm:px-6 lg:px-8 dark:text-white">
            <div class="m-4 py-2">
                <Link
                    as="button"
                    :href="route('administrator.user.create')"
                    class="bg-blue-500 hover:bg-blue-300 text-black px-4 py-3 rounded-md shadow-lg"
                    test-data-id="create-user-button"
                    ><FontAwesomeIcon :icon="faPlus" /> Δημιουργία νέου χρήστη
                </Link>
            </div>
            <div>
                <div class="m-4 py-2 dark:text-white">Διαχειριστές:</div>
                <TransitionGroup name="list" tag="div">
                    <UserCard
                        v-for="(user, index) in users"
                        :key="user.id"
                        :index="index"
                        :user="user"
                        type="admin"
                    />
                </TransitionGroup>
                <div class="m-4 py-2 dark:text-white">Χρήστες ΠΣΔ:</div>
                <TransitionGroup name="list" tag="div">
                    <UserCard
                        v-for="(user, index) in casUsers"
                        :key="user.id"
                        :index="index"
                        :user="user"
                        type="cas"
                    />
                </TransitionGroup>
            </div>
        </div>
    </AppLayout>
</template>
