<script setup lang="ts">
import { ref, watch } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import create from './create.vue';
import edit from './edit.vue';


import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

const props = defineProps<{
  brands: any,
  filters: {
    search?: string,
    is_active?: string | boolean
  }
}>()

const page = usePage()
const createDialog = ref(false);
const editDialog = ref(false);
const editBrand = ref<any | null>(null);
const toast = useToast();
const isDeleting = ref(false);


const filters = ref({
  search: props.filters.search || '',
  is_active: props.filters.is_active || ''
})

function applyFilters() {
  router.get(route('admin.brands.index'), filters.value, {
    preserveState: true,
    replace: true
  })
}

// handle view action
function handleView(id: any) {
  router.visit(route('admin.brands.show', { id }));
}

// Open edit dialog function


// Handle delete action
const handleDelete = (id: number) => {
  if (confirm(`Are you sure you want to delete this Brand?`.concat(String(id)))) {
    isDeleting.value = true;
    router.delete(route('admin.brands.destroy', { id }), {
      preserveScroll: true,
      onSuccess: () => {
        isDeleting.value = false;
        toast.add({ severity: 'warn', summary: 'Warn Message', detail: 'Brand deleted successfully!', life: 3000 });
      },
      onError: (errors) => {
        isDeleting.value = false;
        console.error('Delete failed:', errors);
      },
    });
  }
}


// Returns a severity string for PrimeVue Tag based on is_active value
function getSeverity(brand: any) {
  if (brand.is_active === 1 || brand.is_active === true || brand.is_active === '1') return 'success';
  if (brand.is_active === 0 || brand.is_active === false || brand.is_active === '0') return 'danger';
  return 'info';
}



const getImageUrl = (path: any) => {
    return `/storage/${path}`
}

const createBrandDialog = (value: boolean) => {
    createDialog.value = value;
};
const editBrandDialog = (id: number) => {
   editBrand.value = props.brands.data.find((cat: any) => cat.id === id);
   editDialog.value = true;
};

</script>
<template>
<AdminLayout>

  
   
    
    <h1 class="flex justify-center text-2xl mb-2.5 font-semibold">Brands</h1>
    <div class="flex justify-end-safe items-center gap-4 mb-4">

    <!-- Filters -->
      <InputText v-model="filters.search" @input="applyFilters" type="text" placeholder="Search by brand name" class="input" />

      <select v-model="filters.is_active" @change="applyFilters" class="border rounded px-3 py-2">
        <option value="">All</option>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <Button @click="createBrandDialog(true)">Create Brand</Button>
    </div>
    
    

   
    <DataTable :value="brands.data"
                tableStyle="min-width: 50rem"
                
                selectionMode="single"
    :responsiveLayout="'scroll'"
    dataKey="id"
    :paginator="true"
    :rows="10"
   
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    :rowsPerPageOptions="[5, 10, 25]" >
       <template #header>
        <div class="flex flex-wrap items-center justify-between gap-2">
            <span class="text-xl font-bold">Brands</span>
            <Button icon="pi pi-refresh" rounded raised />
        </div>
       </template>
      <Column field="name" header="Brand Name"></Column>
      <Column field="description" header="Description"></Column>
      <Column field="website" header="Website"></Column>
      <Column header="isActive">
        <template #body="slotProps">
            <Tag :value="slotProps.data.is_active" :severity="getSeverity(slotProps.data)" />
        </template>
    </Column>
          <Column header="Logo">
        <template #body="slotProps">
            <img :src="`${getImageUrl(slotProps.data.logo)}`" :alt="slotProps.data.logo" class="w-24 rounded" />
        </template>
    </Column>
      <Column header="Actions">
        <template #body="{ data }">
          <div class="flex gap-2">
            <Button label="View" class="p-button-sm p-button-outlined" @click="handleView(data.id)" />
            <Button label="Edit" class="p-button-sm p-button-outlined" @click="editBrandDialog(data.id)" />

            <div>
            <Toast />
            <Button label="Delete" severity="warn" @click="handleDelete(data.id)" />
            </div>
          </div>
        </template>
      </Column>


    </DataTable>
<Dialog  v-model:visible="createDialog" :modal="true">
  <create  
  @submit="createBrandDialog(false)"
  />
  
</Dialog>

<Dialog v-model:visible="editDialog" :modal="true" header="Edit Brand" dismissableMask style="width: 40vw">
  <edit
  v-if="editBrand"
  :brand="editBrand"
  @submit="editDialog = false"

  />
</Dialog>

</AdminLayout>
</template>

