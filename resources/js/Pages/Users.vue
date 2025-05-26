<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue';
import { onMounted, ref } from 'vue';
import Swal from 'sweetalert2'

const toast = useToast();

const users = ref([]);
const roles = ref([]);

const getUsers = async () => {
    const response = await axios.get('/api/users');
    users.value = response.data;
}

const getRoles = async () => {
    const response = await axios.get('/api/roles');
    roles.value = response.data;
}

const updateRole = async (id, event) => {
    console.log(id, event);
    try {
        await axios.put('/api/users/' + id, {
           'role_id': event,
        });
    } catch (error)
    {
        console.error(error);
    }
}

onMounted(async () => {
    await getUsers();
    await getRoles();
})
</script>

<template>

    <Head title="Users Dashboard" />
    <Toast />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Users Dashboard
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <DataTable :value="users" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
                    tableStyle="min-width: 50rem">
                    <Column header="User Name">
                        <template #body="slotProps">
                            <span>
                                {{ slotProps.data.first_name }} {{ slotProps.data.last_name }}
                            </span>
                        </template>
                    </Column>

                    <Column header="Action">
                        <template #body="slotProps">
                            <div class="flex gap-2">
                                <Select v-if="slotProps.data.id !== usePage().props.auth.user.id" v-model="slotProps.data.role.id" :options="roles" optionLabel="role_name"
                                    optionValue="id" @update:modelValue="updateRole(slotProps.data.id, $event)" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>