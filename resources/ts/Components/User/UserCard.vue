<script setup lang="ts">
import type { PageWithSharedProps } from "@/pageprops";
import {
    faPencil,
    faUser,
    faUserNinja,
    faUserPlus,
    faBan,
    faTrash,
    faCheck,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Link, router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import { route } from "ziggy";
import Tooltip from "../Tooltip.vue";
import ConfirmationModal from "../ConfirmationModal.vue";
import DangerButton from "../DangerButton.vue";
import SecondaryButton from "../SecondaryButton.vue";
import PrimaryButton from "../PrimaryButton.vue";

const props = defineProps<{
    index: number;
    user: App.Models.User & { role: string };
    type: "admin" | "cas";
}>();

const page = usePage<PageWithSharedProps>();

const showDeleteModal = ref(false);
const showToggleModal = ref(false);
const processing = ref(false);

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deleteUser = () => {
    processing.value = true;
    router.delete(
        route("administrator.user.destroy", [props.user.id, props.type]),
        {
            onFinish: () => {
                processing.value = false;
                showDeleteModal.value = false;
            },
            preserveState: false,
        },
    );
};

const confirmToggle = () => {
    showToggleModal.value = true;
};

const toggleActive = () => {
    processing.value = true;
    router.post(
        route("administrator.user.toggleActive", [props.user.id, props.type]),
        {},
        {
            onFinish: () => {
                processing.value = false;
                showToggleModal.value = false;
            },
            preserveState: false,
        },
    );
};
</script>

<template>
    <div>
        <div
            class="flex flex-wrap flex-row bg-white dark:bg-gray-800 dark:text-white mx-4 my-4 rounded-lg p-4 shadow-lg dark:shadow-md dark:shadow-gray-700 content-center"
            :class="{ 'opacity-50': user.active === false }"
        >
            <div class="py-2 my-auto">
                {{ index + 1 }}.
                <FontAwesomeIcon
                    v-if="user.role === 'Administrator'"
                    :icon="faUserNinja"
                    size="lg"
                    class="mx-1 text-red-500"
                />
                <FontAwesomeIcon
                    v-if="user.role === 'Supervisor'"
                    :icon="faUserPlus"
                    size="lg"
                    class="mx-1 text-blue-500"
                />
                <FontAwesomeIcon
                    v-if="user.role === 'User'"
                    :icon="faUser"
                    size="lg"
                    class="mx-1"
                />
                {{ user.name }}
                <span
                    v-if="user.active === false"
                    class="ml-2 text-xs bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 px-2 py-0.5 rounded"
                >
                    Απενεργοποιημένος
                </span>
            </div>
            <span class="text-blue-500 mx-1 py-2 grow my-auto"
                >({{ user.username }})</span
            >
            <div class="flex items-center">
                <Tooltip
                    :message="
                        user.active
                            ? 'Απενεργοποίηση χρήστη'
                            : 'Ενεργοποίηση χρήστη'
                    "
                >
                    <button
                        @click="confirmToggle"
                        class="transition ease-in-out duration-300 mx-1 hover:shadow-xl hover:-translate-y-0.5 rounded-md px-3 py-2"
                        :class="
                            user.active
                                ? 'hover:bg-yellow-300'
                                : 'hover:bg-green-300'
                        "
                        :test-data-id="`toggle-user-${user.id}-button`"
                    >
                        <FontAwesomeIcon
                            :icon="user.active ? faBan : faCheck"
                            :class="
                                user.active
                                    ? 'text-yellow-500'
                                    : 'text-green-500'
                            "
                        />
                    </button>
                </Tooltip>
                <Tooltip message="Επεξεργασία χρήστη">
                    <Link
                        :href="
                            route('administrator.user.edit', [user.id, type])
                        "
                        class="transition ease-in-out duration-300 mx-1 hover:bg-sky-300 hover:shadow-xl hover:-translate-y-0.5 rounded-md px-3 py-2"
                        :test-data-id="`edit-user-${
                            user.role === 'Administrator' ? 'admin' : 'cas'
                        }-${user.id}-button`"
                        ><FontAwesomeIcon :icon="faPencil" />
                    </Link>
                </Tooltip>
                <Tooltip message="Διαγραφή χρήστη">
                    <button
                        @click="confirmDelete"
                        class="transition ease-in-out duration-300 mx-1 hover:bg-red-300 hover:shadow-xl hover:-translate-y-0.5 rounded-md px-3 py-2"
                        :test-data-id="`delete-user-${user.id}-button`"
                    >
                        <FontAwesomeIcon :icon="faTrash" class="text-red-500" />
                    </button>
                </Tooltip>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            @close="showDeleteModal = false"
        >
            <template #title> Διαγραφή Χρήστη </template>

            <template #content>
                Είστε σίγουροι ότι θέλετε να διαγράψετε τον χρήστη
                {{ user.name }}; Αυτή η ενέργεια δεν μπορεί να αναιρεθεί.
            </template>

            <template #footer>
                <SecondaryButton @click="showDeleteModal = false">
                    Ακύρωση
                </SecondaryButton>

                <DangerButton
                    class="ms-3"
                    :class="{ 'opacity-25': processing }"
                    :disabled="processing"
                    @click="deleteUser"
                >
                    Διαγραφή
                </DangerButton>
            </template>
        </ConfirmationModal>

        <!-- Toggle Active Confirmation Modal -->
        <ConfirmationModal
            :show="showToggleModal"
            @close="showToggleModal = false"
        >
            <template #title>
                {{ user.active ? "Απενεργοποίηση" : "Ενεργοποίηση" }} Χρήστη
            </template>

            <template #content>
                Είστε σίγουροι ότι θέλετε να
                {{ user.active ? "απενεργοποιήσετε" : "ενεργοποιήσετε" }} τον
                χρήστη {{ user.name }};
            </template>

            <template #footer>
                <SecondaryButton @click="showToggleModal = false">
                    Ακύρωση
                </SecondaryButton>

                <PrimaryButton
                    class="ms-3"
                    :class="{ 'opacity-25': processing }"
                    :disabled="processing"
                    @click="toggleActive"
                >
                    {{ user.active ? "Απενεργοποίηση" : "Ενεργοποίηση" }}
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
