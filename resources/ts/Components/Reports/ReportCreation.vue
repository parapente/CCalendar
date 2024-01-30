<script setup lang="ts">
import FormSection from "@/Components/FormSection.vue";
import type { PageWithSharedProps } from "@/pageprops";
import { useForm, usePage } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import route from "ziggy";

const props = defineProps<{
    types: Array<Record<string, string>>;
}>();

const page = usePage<PageWithSharedProps>();

const routePrefix = page.props.auth.user ? "administrator." : "";

const forms = useForm<{
    name: string;
    type: string;
    from: string;
    to: string;
}>({
    name: "",
    type: "1",
    from: DateTime.now().toISODate(),
    to: DateTime.now().plus({ months: 2 }).toISODate(),
});

const fromChanged = (e: Event) => {
    const newDate = (e.target as HTMLInputElement).value;

    forms.to =
        DateTime.fromISO(newDate).plus({ months: 3 }).toISODate() ||
        DateTime.now().toISODate();
};

const toChanged = (e: Event) => {
    const newDate = (e.target as HTMLInputElement).value;

    forms.from =
        DateTime.fromISO(newDate).plus({ months: -3 }).toISODate() ||
        DateTime.now().toISODate();
};

const onSubmit = () => {
    forms.post(route(routePrefix + "report.store"));
};
</script>

<template>
    <FormSection class="m-4">
        <template #title>
            <div class="dark:text-white">Δημιουργία νέας αναφοράς</div>
        </template>
        <template #form>
            <label for="name" class="dark:text-white col-span-2 my-auto"
                >Όνομα αναφοράς:</label
            >
            <input
                id="name"
                type="text"
                class="col-span-4 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Όνομα αναφοράς"
                v-model="forms.name"
                required
            />
            <div
                class="col-span-6 text-red-500 text-sm"
                v-if="forms.errors.name"
            >
                {{ forms.errors.name }}
            </div>
            <label for="type" class="dark:text-white col-span-2 my-auto"
                >Τύπος:</label
            >
            <select
                class="col-span-4 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
                <option v-for="type in types" :value="type.id">
                    {{ type.name }}
                </option>
            </select>
            <div
                class="col-span-6 text-red-500 text-sm"
                v-if="forms.errors.type"
            >
                {{ forms.errors.type }}
            </div>
            <label
                v-if="forms.type === '1'"
                for="from"
                class="dark:text-white col-span-2 my-auto"
            >
                Από:
            </label>
            <input
                v-if="forms.type === '1'"
                id="from"
                type="date"
                class="col-span-4 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                @change="fromChanged"
                v-model="forms.from"
                required
            />
            <div
                class="col-span-6 text-red-500 text-sm"
                v-if="forms.errors.from"
            >
                {{ forms.errors.from }}
            </div>
            <label
                v-if="forms.type === '1'"
                for="to"
                class="dark:text-white col-span-2 my-auto"
            >
                Έως:
            </label>
            <input
                v-if="forms.type === '1'"
                id="to"
                type="date"
                class="col-span-4 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                @change="toChanged"
                v-model="forms.to"
                required
            />
            <div class="col-span-6 text-red-500 text-sm" v-if="forms.errors.to">
                {{ forms.errors.to }}
            </div>
        </template>
        <template #actions>
            <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                @click.prevent="onSubmit"
            >
                Αποθήκευση
            </button>
        </template>
    </FormSection>
</template>
