<script setup lang="ts">
import DropdownList from "@/Components/DropdownList.vue";
import type { PageWithSharedProps } from "@/pageprops";
import { useCalendarStore } from "@/Stores/calendarStore";
import { usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Checkbox from "../Checkbox.vue";

const props = withDefaults(
    defineProps<{
        calendar: string;
        user: string;
        shared: boolean;
        administrator?: boolean;
    }>(),
    {
        calendar: "0",
        user: "0",
        shared: false,
        administrator: false,
    }
);

const page = usePage<PageWithSharedProps>();

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
    "update:calendar": [value: string];
    "update:user": [value: string];
    "update:shared": [value: boolean];
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
                @update:model-value="emit('update:calendar', calendarFilter)"
            >
                <option value="0">Όλα</option>
                <option
                    v-for="calendar in activeCalendars"
                    :key="calendar.id"
                    :value="calendar.id"
                >
                    {{
                        calendar.shared
                            ? `${calendar.name} (Κοινόχρηστο)`
                            : calendar.name
                    }}
                </option>
            </DropdownList>
            <label
                v-if="
                    administrator || page.props.cas_user_role === 'Supervisor'
                "
                for="userFilter"
                class="mr-2 my-auto"
                >Χρήστες:</label
            >
            <DropdownList
                v-if="
                    administrator || page.props.cas_user_role === 'Supervisor'
                "
                v-model="userFilter"
                class="text-black col-span-5"
                @update:model-value="emit('update:user', userFilter)"
            >
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
        <div class="p-2">
            <label for="show_shared" class="dark:text-white mx-2"
                >Εμφάνιση κοινόχρηστων ημερολογίων</label
            >
            <Checkbox
                id="show_shared"
                :checked="shared"
                @update:checked="(value) => emit('update:shared', value)"
            />
        </div>
    </div>
</template>
