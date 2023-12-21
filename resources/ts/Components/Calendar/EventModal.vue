<script setup lang="ts">
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import Modal from "../Modal.vue";
import EventCard from "./EventCard.vue";
import { faX } from "@fortawesome/free-solid-svg-icons";

const props = withDefaults(
    defineProps<{
        event:
            | (App.Models.CalendarEvent & { cas_user?: App.Models.CasUser })
            | null;
        show: boolean;
    }>(),
    {
        show: false,
    }
);

const emit = defineEmits<{
    "update:show": [show: boolean];
    editEvent: [id: number];
    deleteEvent: [id: number];
}>();
</script>

<template>
    <Modal closable="true" :show="show" @close="emit('update:show', false)">
        <div class="flex w-full">
            <button
                v-on:click="emit('update:show', false)"
                class="ml-auto mr-3 mt-2 dark:text-white"
            >
                <FontAwesomeIcon :icon="faX" />
            </button>
        </div>
        <EventCard
            :event="event!"
            v-if="event"
            class="dark:text-white m-2"
            @editEvent="emit('editEvent', event.id)"
            @deleteEvent="emit('deleteEvent', event.id)"
        ></EventCard>
    </Modal>
</template>
