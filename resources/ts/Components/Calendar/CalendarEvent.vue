<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import { watch } from "vue";

const props = withDefaults(
    defineProps<{
        eventDate?: string;
        visible?: boolean;
    }>(),
    {
        eventDate: DateTime.local().toISODate(),
        visible: false,
    }
);

const form = useForm<{
    title: string;
    description: string;
    event_type: number;
    start_date: string;
    end_date: string;
    location: string;
    url: string;
}>({
    title: "",
    description: "",
    event_type: 1,
    start_date: props.eventDate + "T00:00",
    end_date: props.eventDate + "T23:59",
    location: "",
    url: "",
});

const onSubmit = () => {};

watch(
    () => props.eventDate,
    (value) => {
        form.start_date = value + "T" + DateTime.now().toFormat("HH:mm");
        form.end_date =
            value + "T" + DateTime.now().plus({ hour: 1 }).toFormat("HH:mm");
    }
);
</script>

<template>
    <div
        v-if="props.visible"
        class="fixed top-5 left-5 w-11/12 max-w-3xl bg-white border border-black rounded-lg p-2"
    >
        Νέα εκδήλωση
        <form @submit.prevent="onSubmit" class="flex flex-col">
            <label for="title">Τίτλος εκδήλωσης:</label>
            <input
                type="text"
                name="title"
                v-model="form.title"
                placeholder="Τίτλος"
            />
            <label for="description">Περιγραφή:</label>
            <textarea
                name="description"
                v-model="form.description"
                placeholder="Λεπτομέρειες εκδήλωσης"
            />
            <label for="event_type">Τύπος εκδήλωσης:</label>
            <select name="event_type" v-model="form.event_type">
                <option value="1">Ημερίδα/Σεμινάριο</option>
                <option value="2">Επίσκεψη σε σχολική μονάδα</option>
            </select>
            <label for="start_date">Ημερομηνία Εκδήλωσης:</label>
            <input
                type="datetime-local"
                name="start_date"
                v-model="form.start_date"
            />
            <label for="end_date">Ημερομηνία Λήξης:</label>
            <input
                type="datetime-local"
                name="start_date"
                v-model="form.end_date"
            />
            <label for="location">Τοποθεσία:</label>
            <input
                type="text"
                name="location"
                v-model="form.location"
                placeholder="πχ. Θεσσαλονίκη ή συγκρότημα σχολικών μονάδων"
            />
            <label for="url">Σχετική ιστοσελίδα:</label>
            <input
                type="url"
                name="url"
                v-model="form.url"
                placeholder="http://www.example.com"
            />
            <div class="flex justify-between mt-2">
                <button
                    type="button"
                    class="px-3 py-2 border border-red-600 bg-red-600 text-white hover:bg-red-400 rounded-lg"
                    @click="$emit('update:visible', false)"
                >
                    Άκυρο
                </button>
                <button
                    type="submit"
                    class="px-3 py-2 border border-green-600 bg-green-600 text-white hover:bg-green-400 rounded-lg"
                >
                    Αποθήκευση
                </button>
            </div>
        </form>
    </div>
</template>
