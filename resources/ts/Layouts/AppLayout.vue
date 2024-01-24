<script setup lang="ts">
import { ref } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import route from "ziggy";
import type { PageWithSharedProps } from "@/pageprops";

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route("logout"));
};

const page = usePage<PageWithSharedProps>();
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 print:bg-white">
            <nav
                class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 print:hidden"
            >
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex gap-3">
                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:flex">
                                <NavLink
                                    :href="route('administrator.index')"
                                    :active="
                                        route().current('administrator.index')
                                    "
                                >
                                    Αρχική
                                </NavLink>
                            </div>
                            <div class="hidden space-x-8 sm:-my-px sm:flex">
                                <NavLink
                                    :href="route('administrator.user.index')"
                                    :active="
                                        route().current(
                                            'administrator.user.index'
                                        )
                                    "
                                >
                                    Χρήστες
                                </NavLink>
                            </div>
                            <div class="hidden space-x-8 sm:-my-px sm:flex">
                                <NavLink
                                    :href="
                                        route('administrator.calendar.index')
                                    "
                                    :active="
                                        route().current(
                                            'administrator.calendar.index'
                                        )
                                    "
                                >
                                    Ημερολόγια
                                </NavLink>
                            </div>
                            <div class="hidden space-x-8 sm:-my-px sm:flex">
                                <NavLink
                                    :href="
                                        route('administrator.calendar.overview')
                                    "
                                    :active="
                                        route().current(
                                            'administrator.calendar.overview'
                                        )
                                    "
                                >
                                    Εποπτεία
                                </NavLink>
                            </div>
                            <div class="hidden space-x-8 sm:-my-px sm:flex">
                                <NavLink
                                    :href="route('administrator.report.index')"
                                    :active="
                                        route().current(
                                            'administrator.report.index'
                                        )
                                    "
                                >
                                    Αναφορές
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                                            >
                                                {{ page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->

                                        <DropdownLink
                                            :href="route('profile.show')"
                                        >
                                            Προφίλ
                                        </DropdownLink>

                                        <div
                                            class="border-t border-gray-200 dark:border-gray-600"
                                        />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Αποσύνδεση
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('administrator.index')"
                            :active="route().current('administrator.index')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600"
                    >
                        <div class="flex items-center px-4">
                            <div>
                                <div
                                    class="font-medium text-base text-gray-800 dark:text-gray-200"
                                >
                                    {{ page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ page.props.auth.user.username }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink
                                :href="route('profile.show')"
                                :active="route().current('profile.show')"
                            >
                                Προφίλ
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Αποσύνδεση
                                </ResponsiveNavLink>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                v-if="$slots.header"
                class="bg-white dark:bg-gray-800 shadow"
            >
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
