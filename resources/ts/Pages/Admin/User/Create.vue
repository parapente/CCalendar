<script setup lang="ts">
import { router, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import DangerButton from "@/Components/DangerButton.vue";
import route from "ziggy";
import DropdownList from "@/Components/DropdownList.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps<{
    roles: Array<App.Models.Role>;
}>();

const form = useForm<{
    name: string;
    username: string;
    employee_number: string;
    password: string;
    password_confirmation: string;
    type: "admin" | "cas";
    role_id: number;
}>({
    name: "",
    username: "",
    employee_number: "",
    password: "",
    password_confirmation: "",
    type: "admin",
    role_id: props.roles[0].id,
});

const submit = () => {
    form.post(route("administrator.user.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <AppLayout title="Δημιουργία νέου χρήστη">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Χρήστες
            </h2>
        </template>

        <div class="py-4">
            <AuthenticationCard>
                <div
                    class="text-3xl text-bold mt-2 mb-4 p-5 bg-slate-300 rounded-lg shadow-md text-center"
                >
                    Δημιουργία νέου χρήστη
                </div>
                <form @submit.prevent="submit">
                    <div class="mt-4">
                        <InputLabel for="type" value="Είδος Λογαριασμού" />
                        <DropdownList
                            id="type"
                            class="w-full"
                            v-model="form.type"
                        >
                            <option
                                v-for="type in ['admin', 'cas']"
                                :value="type"
                            >
                                {{ type }}
                            </option>
                        </DropdownList>
                    </div>

                    <div>
                        <InputLabel for="name" value="Όνομα" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="username" value="Όνομα χρήστη" />
                        <TextInput
                            id="username"
                            v-model="form.username"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autocomplete="username"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.username"
                        />
                    </div>

                    <div class="mt-4" v-if="form.type === 'cas'">
                        <InputLabel
                            for="employee_number"
                            value="ΑΜ χρήστη στο ΠΣΔ"
                        />
                        <TextInput
                            id="employee_number"
                            v-model="form.employee_number"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autocomplete="employee_number"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.employee_number"
                        />
                    </div>

                    <div class="mt-4" v-if="form.type === 'admin'">
                        <InputLabel for="password" value="Κωδικός" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password"
                        />
                    </div>

                    <div class="mt-4" v-if="form.type === 'admin'">
                        <InputLabel
                            for="password_confirmation"
                            value="Επιβεβαίωση κωδικού"
                        />
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password_confirmation"
                        />
                    </div>

                    <div class="mt-4" v-if="form.type === 'cas'">
                        <InputLabel
                            for="role"
                            value="Κατηγορία λογαριασμού ΠΣΔ"
                        />
                        <DropdownList
                            id="role"
                            class="w-full"
                            v-model="form.role_id"
                        >
                            <option v-for="role in roles" :value="role.id">
                                {{ role.name }}
                            </option>
                        </DropdownList>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <DangerButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            @click="
                                router.get(route('administrator.user.index'))
                            "
                        >
                            ΑΚΥΡΩΣΗ
                        </DangerButton>
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            ΑΠΟΘΗΚΕΥΣΗ
                        </PrimaryButton>
                    </div>
                </form>
            </AuthenticationCard>
        </div>
    </AppLayout>
</template>
