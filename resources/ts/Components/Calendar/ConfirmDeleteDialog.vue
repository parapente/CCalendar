<script setup lang="ts">
import DialogModal from "@/Components/DialogModal.vue";

const props = defineProps<{
    show: boolean;
}>();

const emit = defineEmits<{
    deleteEvent: [value: number];
    answer: [value: "yes" | "no"];
}>();
</script>

<template>
    <DialogModal :show="props.show" @close="emit('answer', 'no')">
        <template #title> Επιβεβαίωση διαγραφής εκδήλωσης </template>
        <template #content>
            <p>Είστε σίγουροι ότι θέλετε να διαγράψετε την εκδήλωση;</p>
        </template>
        <template #footer>
            <div class="flex w-full">
                <button
                    @click="emit('answer', 'no')"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-auto"
                    test-data-id="confirm-delete-no-button"
                >
                    Όχι
                </button>
                <button
                    @click="emit('answer', 'yes')"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                    test-data-id="confirm-delete-yes-button"
                >
                    Ναι
                </button>
            </div>
        </template>
    </DialogModal>
</template>
