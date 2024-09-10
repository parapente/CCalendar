<script setup lang="ts">
import Checkbox from "@/Components/Checkbox.vue";
import FormSection from "@/Components/FormSection.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import route from "ziggy";

const props = defineProps<{
    calendar: App.Models.Calendar;
}>();

const forms = useForm({
    name: props.calendar.name,
    color: props.calendar.color,
    active: props.calendar.active == true ? true : false,
    shared: props.calendar.shared == true ? true : false,
});

const onSubmit = () => {
    forms.put(
        route("administrator.calendar.update", { calendar: props.calendar })
    );
};
</script>

<template>
    <AppLayout>
        <FormSection class="m-4">
            <template #title>
                <div class="dark:text-white">Επεξεργασία ημερολογίου</div>
            </template>
            <template #form>
                <label for="name" class="dark:text-white col-span-2 my-auto"
                    >Όνομα ημερολογίου:</label
                >
                <input
                    id="name"
                    type="text"
                    class="col-span-4 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Όνομα ημερολογίου"
                    v-model="forms.name"
                    required
                />
                <div
                    class="col-span-6 text-red-500 text-sm"
                    v-if="forms.errors.name"
                >
                    {{ forms.errors.name }}
                </div>
                <label for="color" class="dark:text-white col-span-2 my-auto"
                    >Χρώμα ημερολογίου:</label
                >
                <input
                    id="color"
                    type="color"
                    class="col-span-4 p-1 rounded-md"
                    v-model="forms.color"
                    required
                />
                <div
                    class="col-span-6 text-red-500 text-sm"
                    v-if="forms.errors.color"
                >
                    {{ forms.errors.color }}
                </div>
                <label for="active" class="dark:text-white col-span-2 my-auto"
                    >Ενεργό:</label
                >
                <Checkbox
                    id="active"
                    name="active"
                    class="col-span-4 my-auto"
                    v-model:checked="forms.active"
                />
                <div
                    class="col-span-6 text-red-500 text-sm"
                    v-if="forms.errors.active"
                >
                    {{ forms.errors.active }}
                </div>
                <label for="shared" class="dark:text-white col-span-2 my-auto"
                    >Κοινόχρηστο:</label
                >
                <Checkbox
                    id="shared"
                    name="shared"
                    class="col-span-4 my-auto"
                    v-model:checked="forms.shared"
                />
                <div
                    class="col-span-6 text-red-500 text-sm"
                    v-if="forms.errors.shared"
                >
                    {{ forms.errors.shared }}
                </div>
            </template>
            <template #actions>
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    @click.prevent="onSubmit"
                    test-data-id="submit-button"
                >
                    Αποθήκευση
                </button>
            </template>
        </FormSection>
    </AppLayout>
</template>
