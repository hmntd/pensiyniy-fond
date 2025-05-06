<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue';
import { onMounted, ref } from 'vue';
import Swal from 'sweetalert2'

const toast = useToast();

const pensions = ref([]);

const getPensions = async () => {
    const response = await axios.get('/api/pensions');
    pensions.value = response.data;
}

const deletePension = async (id) => {
    Swal.fire({
        title: "Do you want to delete this pension?",
        showDenyButton: true,
        confirmButtonText: "Don't delete",
        denyButtonText: `Delete`
    }).then(async (result) => {
        if (result.isConfirmed) {
            Swal.fire("Not deleted", "", "info");
        } else if (result.isDenied) {
            await axios.delete('/api/pensions/' + id);
            await getPensions();
            Swal.fire("Deleted!", "", "success");
        }
    });
}

onMounted(async () => {
    await getPensions();
})
</script>

<template>

    <Head title="Pensions Dashboard" />
    <Toast />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Pensions Dashboard
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <DataTable v-if="pensions.length > 0" :value="pensions" paginator :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 50rem">
                    <Column header="Client Name">
                        <template #body="slotProps">
                            <span>
                                {{ slotProps.data.client.first_name }} {{ slotProps.data.client.last_name }}
                            </span>
                        </template>
                    </Column>

                    <Column header="User Name">
                        <template #body="slotProps">
                            <span>
                                {{ slotProps.data.user.first_name }} {{ slotProps.data.user.last_name }}
                            </span>
                        </template>
                    </Column>

                    <Column header="Pension Amount">
                        <template #body="slotProps">
                            <span>
                                {{ slotProps.data.pension }}
                            </span>
                        </template>
                    </Column>

                    <Column header="Action">
                        <template #body="slotProps">
                            <div class="flex gap-2">
                                <Button severity="danger" @click="deletePension(slotProps.data.id)">Delete</Button>
                            </div>
                        </template>
                    </Column>
                </DataTable>
                <div v-else class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        No pensions found
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>