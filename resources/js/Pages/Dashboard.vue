<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import dayjs from 'dayjs'
import { useToast } from 'primevue';
import Swal from 'sweetalert2';
import { computed, onMounted, ref, watch } from 'vue';

const form = ref({
    first_name: '',
    last_name: '',
    date_of_birth: '',
    errors: {
        first_name: '',
        last_name: '',
        date_of_birth: '',
    }
});

const formBenefit = ref({
    id: null,
    errors: null,
})

const salaryEntries = ref([]);

const clients = ref([]);
const benefits = ref([]);
const page = usePage();
const toast = useToast();
const columns = ref([
    {
        field: 'first_name',
        header: 'First Name',
    },
    {
        field: 'last_name',
        header: 'Last Name',
    },
    {
        field: 'date_of_birth',
        header: 'Date of Birth',
    }
])

const getClients = async () => {
    const response = await axios.get('/api/clients');
    clients.value = response.data;
}

const getBenefits = async () => {
    const response = await axios.get('/api/benefits');
    benefits.value = response.data;
}

const showModal = ref(false);
const showSalaryModal = ref(0);
const showBenefitModal = ref(0);
const showDocumentModal = ref(0);
const showSalary = computed(() => showSalaryModal.value > 0);
const showBenefit = computed(() => showBenefitModal.value > 0);
const showDocument = computed(() => showDocumentModal.value > 0);

watch(() => showBenefitModal.value, (newVal) => {
    if (newVal) {
        let client = clients.value.find(client => client.id === newVal);
        if (client.benefits[0]) {
            formBenefit.value.id = client.benefits[0].id;
        }
    }
});

watch(() => showSalaryModal.value, (newVal) => {
    let client = clients.value.find(client => client.id === newVal);
    if (client) {
        salaryEntries.value = client.salaries.map(salary => ({
            ...salary,
            inRange: false,
        }));
    }
})

const addEntry = () => {
    salaryEntries.value.push({
        company_name: '',
        job_title: '',
        salary: null,
        date: null,
        has_benefit: false,
        client_id: showSalaryModal.value,
        inRange: false,
    })
}

const removeEntry = (index) => {
    salaryEntries.value.splice(index, 1)
}

const createClient = async () => {
    form.value.errors = {
        first_name: form.value.first_name ? '' : 'First name is required',
        last_name: form.value.last_name ? '' : 'Last name is required',
        date_of_birth: form.value.date_of_birth ? (
            new Date(form.value.date_of_birth) > new Date()
                ? 'Date of birth cannot be in the future'
                : null
        ) : 'Date of birth is required',
    }

    if (
        form.value.errors.first_name ||
        form.value.errors.last_name ||
        form.value.errors.date_of_birth
    ) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: form.value.errors.date_of_birth + '\n' + form.value.errors.last_name + '\n' + form.value.errors.first_name, life: 3000 })
        return
    }

    if (form.value.first_name && form.value.last_name && form.value.date_of_birth) {
        await axios.post('/api/clients', {
            first_name: form.value.first_name,
            last_name: form.value.last_name,
            date_of_birth: form.value.date_of_birth,
            user_id: page.props.auth.user.id
        });

        await getClients();
        showModal.value = false;
        form.value = {
            first_name: '',
            last_name: '',
            date_of_birth: '',
            errors: {
                first_name: '',
                last_name: '',
                date_of_birth: '',
            }
        }
    }
}

const createSalary = async () => {
    for (const salaryData of salaryEntries.value) {
        if (
            !salaryData.date ||
            !salaryData.company_name ||
            !salaryData.job_title ||
            !salaryData.salary
        ) {
            toast.add({ severity: 'warn', summary: 'Validation', detail: 'All fields are required.', life: 3000 })
            return
        }

        if (
            new Date(salaryData.date) > new Date()
        ) {
            toast.add({ severity: 'warn', summary: 'Validation', detail: 'Date cannot be in the future.', life: 3000 })
            return
        }

        if (new Date(salaryData.date) < new Date(clients.value.find(client => client.id === showSalaryModal.value).date_of_birth)) {
            toast.add({ severity: 'warn', summary: 'Validation', detail: 'Date cannot be before date of birth.', life: 3000 })
            return

        }
    }

    await axios.post('/api/salaries/destroy', {
        client_id: showSalaryModal.value,
    });

    for (const salaryData of salaryEntries.value) {
        if (!salaryData.inRange) {
            await axios.post('/api/salaries', {
                client_id: showSalaryModal.value,
                company_name: salaryData.company_name,
                job_title: salaryData.job_title,
                salary: salaryData.salary,
                date: salaryData.date,
                has_benefit: salaryData.has_benefit
            })
        } else {
            const [start, end] = salaryData.date;

            let current = dayjs(start);
            const endMonth = dayjs(end);
            while (current.isBefore(endMonth)) {
                const firstOfMonth = current.startOf('month').format('YYYY-MM-DD');

                await axios.post('/api/salaries', {
                    client_id: showSalaryModal.value,
                    company_name: salaryData.company_name,
                    job_title: salaryData.job_title,
                    salary: salaryData.salary,
                    date: firstOfMonth,
                    has_benefit: salaryData.has_benefit
                })

                current = current.add(1, 'month');
            }
        }
    }

    toast.add({ severity: 'success', summary: 'Created', detail: 'Salaries created!', life: 3000 })
    showSalaryModal.value = 0;
}

const createBenefit = async () => {
    await axios.post('/api/client/benefits', {
        client_id: showBenefitModal.value,
        benefit_id: formBenefit.value.id,
    });

    toast.add({ severity: 'success', summary: 'Created', detail: 'Benefit added!', life: 3000 })
    showBenefitModal.value = 0;
}

const onAdvancedUpload = async (event, type) => {
    console.log('event', event, type);
    if (type === 'passport') {
        await axios.put(`/api/clients/${showDocumentModal.value}`, {
            'uploaded_passport': true,
        });
    } else {
        await axios.put(`/api/clients/${showDocumentModal.value}`, {
            'uploaded_work_book': true,
        });
    }

    await axios.post('/api/document/upload', {
        files: event.files[0],
        client_id: showDocumentModal.value,
        type: type,
    });
}

const calculatePension = async (client_id) => {
    try {
        await axios.post('/api/pensions', {
            client_id: client_id,
            user_id: page.props.auth.user.id,
        });
    } catch (error) {
        console.log(error);
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message, life: 3000 })
    }
}

const deleteClient = async (client_id) => {
    Swal.fire({
        title: "Do you want to delete this client?",
        showDenyButton: true,
        confirmButtonText: "Don't delete",
        denyButtonText: `Delete`
    }).then(async (result) => {
        if (result.isConfirmed) {
            Swal.fire("Not deleted", "", "info");
        } else if (result.isDenied) {
            await axios.delete('/api/clients/' + client_id);
            await getClients();
            Swal.fire("Deleted!", "", "success");
        }
    });
}

onMounted(async () => {
    await getClients();
    await getBenefits();
})
</script>

<template>

    <Head title="Clients Dashboard" />
    <Toast />

    <Dialog v-model:visible="showModal" header="Create Client" :style="{ width: '75vw' }" maximizable modal
        :contentStyle="{ height: '300px' }">
        <div class="flex gap-[24px] items-center justify-center h-full">
            <FloatLabel variant="on">
                <InputText id="firstName" v-model="form.first_name" autofocus :invalid="form.errors.first_name" />
                <label for="firstName">First name</label>
            </FloatLabel>

            <FloatLabel variant="on">
                <InputText id="lastName" v-model="form.last_name" :invalid="form.errors.last_name" />
                <label for="lastName">Last name</label>
            </FloatLabel>

            <FloatLabel variant="on">
                <DatePicker v-model="form.date_of_birth" inputId="dob" showIcon iconDisplay="input"
                    :invalid="form.errors.date_of_birth" :maxDate="new Date()" />
                <label for="dob">Date of birth</label>
            </FloatLabel>
        </div>
        <template #footer>
            <Button label="Cancel" severity="secondary" @click="showModal = false" />
            <Button label="Create" severity="primary" @click="createClient" />
        </template>
    </Dialog>

    <Dialog v-model:visible="showSalary" header="Create Salary" :style="{ width: '75vw' }" maximizable modal
        :contentStyle="{ height: '300px' }">
        <div class="flex flex-col p-[48px] gap-[32px] items-center justify-center">
            <div v-for="(entry, index) in salaryEntries" :key="index"
                class="flex w-full justify-center flex-wrap gap-3 items-end">
                <FloatLabel class="">
                    <InputText v-model="entry.company_name" :inputId="'companyName-' + index" />
                    <label :for="'companyName-' + index">Company name</label>
                </FloatLabel>

                <FloatLabel class="">
                    <InputText v-model="entry.job_title" :inputId="'jobTitle-' + index" />
                    <label :for="'jobTitle-' + index">Job title</label>
                </FloatLabel>

                <FloatLabel class="">
                    <InputText v-model="entry.salary" :inputId="'salary-' + index" type="number" />
                    <label :for="'salary-' + index">Salary</label>
                </FloatLabel>

                <FloatLabel class="" v-if="!entry.inRange">
                    <Calendar v-model="entry.date" dateFormat="yy-mm-dd" showIcon iconDisplay="input"
                        :inputId="'date-' + index" />
                    <label :for="'date-' + index">Date</label>
                </FloatLabel>

                <FloatLabel class="" v-else>
                    <DatePicker v-model="entry.date" selectionMode="range" showIcon iconDisplay="input"
                        :inputId="'date-' + index" />
                    <label :for="'date-' + index">Date</label>
                </FloatLabel>

                <ToggleButton v-model="entry.inRange" onLabel="Date is Range" offLabel="Date is Single" />

                <ToggleButton v-model="entry.has_benefit" onLabel="Extrimal work" offLabel="Not Extrimal" />

                <Button severity="danger" outlined @click="removeEntry(index)">Delete</Button>
            </div>

        </div>
        <template #footer>
            <Button label="Add" outlined @click="addEntry" class="w-fit self-start" />
            <Button label="Cancel" severity="secondary" @click="showSalaryModal = 0" />
            <Button label="Create" severity="primary" @click="createSalary" />
        </template>
    </Dialog>

    <Dialog v-model:visible="showBenefit" header="Add Benefit" :style="{ width: '75vw' }" maximizable modal
        :contentStyle="{ height: '300px' }">
        <div class="flex gap-[24px] items-center justify-center h-full">
            <FloatLabel variant="on" class="w-[10%]">
                <Select id="benefit" class="w-full" v-model="formBenefit.id" autofocus :invalid="formBenefit.errors"
                    :options="benefits.filter((b) => b.type === 'static')" optionLabel="name" optionValue="id" />
                <label for="benefit">Benefit</label>
            </FloatLabel>
        </div>
        <template #footer>
            <Button label="Cancel" severity="secondary" @click="showBenefitModal = false" />
            <Button label="Create" severity="primary" @click="createBenefit" />
        </template>
    </Dialog>

    <Dialog v-model:visible="showDocument" header="Upload Documents" :style="{ width: '75vw' }" maximizable modal
        :contentStyle="{ height: '300px' }">
        <div class="flex gap-[24px] items-center justify-center h-full">
            <FileUpload v-if="!clients.find((c) => c.id === showDocumentModal).uploaded_passport" name="workbook"
                @uploader="onAdvancedUpload($event, 'passport')" :customUpload="true" :auto="true" accept="image/*"
                :maxFileSize="1000000000">
                <template #empty>
                    <span>Passport upload</span>
                </template>
            </FileUpload>

            <FileUpload v-if="!clients.find((c) => c.id === showDocumentModal).uploaded_work_book" name="workbook"
                @uploader="onAdvancedUpload($event, 'workbook')" :customUpload="true" :auto="true" accept="image/*"
                :maxFileSize="1000000000">
                <template #empty>
                    <span>Workbook upload</span>
                </template>
            </FileUpload>
        </div>
        <template #footer>
            <Button label="Cancel" severity="secondary" @click="showDocumentModal = 0" />
            <Button label="Create" severity="primary" @click="uploadDocuments" />
        </template>
    </Dialog>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Clients Dashboard
                </h2>
                <Button v-if="$page.props.auth.user.role_id === 1"  @click="showModal = true" severity="primary">
                    + Create Client
                </Button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <DataTable v-if="clients.length > 0" :value="clients" paginator :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 50rem">
                    <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header"></Column>
                    <Column v-if="$page.props.auth.user.role_id === 1" header="Actions">
                        <template #body="slotProps">
                            <div class="flex gap-2">
                                <Button severity="secondary" @click="showSalaryModal = slotProps.data.id">+ Add
                                    Salary</Button>
                                <Button severity="secondary" @click="showBenefitModal = slotProps.data.id">+ Add
                                    Benefit</Button>
                                <Button v-if="!slotProps.data.uploaded_passport && !slotProps.data.uploaded_work_book"
                                    severity="secondary" @click="showDocumentModal = slotProps.data.id">Upload
                                    Documents</Button>
                                <Button @click="calculatePension(slotProps.data.id)">Calculate Pension</Button>
                                <Button severity="danger" @click="deleteClient(slotProps.data.id)">Delete
                                    Client</Button>
                            </div>
                        </template>
                    </Column>
                </DataTable>
                <div v-else class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        No clients found
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
