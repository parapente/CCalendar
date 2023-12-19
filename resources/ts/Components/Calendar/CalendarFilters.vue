<script setup lang="ts">
import DropdownList from "@/Components/DropdownList.vue";
import { useCalendarStore } from "@/Stores/calendarStore";
import { computed, ref } from "vue";

const props = withDefaults(
    defineProps<{
        calendar: number;
        user: number;
    }>(),
    {
        calendar: 0,
        user: 0,
    }
);

const calendarFilter = ref(props.calendar);

const userFilter = ref(props.user);

const calendarStore = useCalendarStore();

const activeCalendars = computed(() =>
    calendarStore.calendars.filter((calendar) => {
        return calendar.active;
    })
);

const calendarUsers = computed(() => {
    return calendarStore.calendarEvents
        .map((event) => event.cas_user)
        .filter((user, index, self) => {
            return self.findIndex((other) => other?.id === user?.id) === index;
        });
});

const emit = defineEmits<{
    "update:calendar": [value: number];
    "update:user": [value: number];
}>();
</script>

<template>
    <div class="flex flex-col w-full dark:text-white items-center my-2">
        <div class="mb-1">Φίλτρα:</div>
        <div class="grid grid-cols-6 gap-y-2">
            <label for="calendarFilter" class="mr-2 my-auto">Ημερολόγιο:</label>
            <DropdownList
                v-model="calendarFilter"
                class="text-black col-span-5"
            >
                <option value="0">Όλα</option>
                <option
                    v-for="calendar in activeCalendars"
                    :key="calendar.id"
                    :value="calendar.id"
                >
                    {{ calendar.name }}
                </option>
            </DropdownList>
            <label for="userFilter" class="mr-2 my-auto">Χρήστες:</label>
            <DropdownList v-model="userFilter" class="text-black col-span-5">
                <option value="0">Όλα</option>
                <option
                    v-for="user in calendarUsers"
                    :key="user?.id"
                    :value="user?.id"
                >
                    {{ user?.name }}
                </option>
            </DropdownList>
        </div>
    </div>
</template>
