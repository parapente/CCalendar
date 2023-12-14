<script setup lang="ts">
import { useCalendarStore } from "@/Stores/calendarStore";
import { useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import { watch } from "vue";

const props = withDefaults(
    defineProps<{
        event: App.Models.CalendarEvent;
        visible?: boolean;
    }>(),
    {
        visible: false,
    }
);

const calendarStore = useCalendarStore();

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
    event_type: calendarStore.calendars.map((calendar) => calendar.id)[0],
    start_date: props.event.start_date,
    end_date: props.event.end_date,
    location: "",
    url: "",
});

const emit = defineEmits<{
    save: [value: App.Models.CalendarEvent];
    "update:visible": [value: boolean];
}>();

const onSubmit = () => {
    emit("save", {
        id: 0,
        title: form.title,
        description: form.description,
        start_date: form.start_date,
        end_date: form.end_date,
        location: form.location,
        url: form.url,
        calendar_id: form.event_type,
        created_at: null,
        updated_at: null,
    });

    form.reset();
    emit("update:visible", false);
};

watch(
    () => props.event,
    (value) => {
        const currentTime = DateTime.local();
        const newStartDate =
            DateTime.fromISO(value.start_date)
                .plus({
                    hours: currentTime.hour,
                    minutes: currentTime.minute,
                })
                .toISO({
                    includeOffset: false,
                    suppressMilliseconds: true,
                    suppressSeconds: true,
                }) ?? "";

        console.log(`New date: ${newStartDate}`);

        form.title = value.title;
        form.description = value.description;
        form.start_date = form.end_date = newStartDate;
        form.location = value.location;
        form.url = value.url;
    },
    {
        deep: true,
    }
);
</script>

<template>
    <div
        v-if="props.visible"
        class="fixed top-5 left-5 w-11/12 max-w-3xl bg-white border border-black rounded-lg shadow-black shadow-2xl"
    >
        <div class="bg-slate-300 p-2 rounded-t-md">Νέα εκδήλωση</div>
        <form @submit.prevent="onSubmit" class="flex flex-col p-2">
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
                <option
                    v-for="event_type in calendarStore.calendars.map(
                        (calendar) => {
                            return { id: calendar.id, name: calendar.name };
                        }
                    )"
                    :value="event_type.id"
                >
                    {{ event_type.name }}
                </option>
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
                    @click="emit('update:visible', false)"
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
