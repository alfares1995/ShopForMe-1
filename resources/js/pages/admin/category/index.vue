<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { useToast } from "primevue/usetoast";
import Toast from 'primevue/toast';
import create from './create.vue';
import edit from './edit.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Admin Dashboard', href: '/admin/dashboard' },
  { title: 'Category', href: '/admin/category' },
];

// Props
const props = defineProps<{
  categories: any;
  parentCategories: any;
  filters: {
    search?: string;
    is_active?: string | boolean;
  };
}>();

// Reactive state
const visible = ref(false);
const editVisible = ref(false);
const submitted = ref(false);
const isDeleting = ref(false);
const toast = useToast();
const page = usePage();
const editCategory = ref<any | null>(null);

// Filters
const filters = ref({
  search: props.filters.search || '',
  is_active: props.filters.is_active || '',
});

// Watch search with debounce
watch(
  () => filters.value.search,
  debounce(() => applyFilters(), 400)
);

////////////////////////////////////////// Functions And Handlers Area////////////////////////////////
// Apply filters function
const applyFilters = () => {
  router.get(route('admin.category.index'), filters.value, {
    preserveState: true,
    replace: true,
  });
}

// Handle view action
const handleView = (id: any) => {
  router.visit(route('admin.category.show', { id }));
}

// Open edit dialog function
const openEditDialog = (id: number) => {
  editCategory.value = props.categories.data.find((cat: any) => cat.id === id);
  editVisible.value = true;
}

// Handle delete action
const handleDelete = (id: number) => {
  if (confirm(`Are you sure you want to delete this Category?`)) {
    isDeleting.value = true;
    router.delete(route('admin.category.destroy', { id }), {
      preserveScroll: true,
      onSuccess: () => {
        isDeleting.value = false;
        toast.add({ severity: 'warn', summary: 'Warn Message', detail: 'Category deleted successfully!', life: 3000 });
      },
      onError: (errors) => {
        isDeleting.value = false;
        console.error('Delete failed:', errors);
      },
    });
  }
}

// Get severity based on is_active status
const getSeverity = (category: any) => {
  if (category.is_active === 1 || category.is_active === true || category.is_active === '1') return 'success';
  if (category.is_active === 0 || category.is_active === false || category.is_active === '0') return 'danger';
  return 'info';
}

</script>

<template>
  <Head title="Category" />

  <AdminLayout :breadcrumbs="breadcrumbs">
    <!-- Filters -->
    <div class="flex items-center gap-4 mb-4">
      <InputText
        v-model="filters.search"
        type="text"
        placeholder="Search by category name"
        class="input w-64"
      />

      <select v-model="filters.is_active" @change="applyFilters" class="border rounded px-3 py-2">
        <option value="">All</option>
        <option value= "0" >Inactive</option>
        <option value= "1" >Active</option>
      </select>

      <Button @click="visible = true" label="Create Category" />
    </div>

    <!-- Table -->
    <DataTable
      :value="categories.data"
      dataKey="id"
      tableStyle="min-width: 50rem"
      :paginator="true"
      :rows="categories.per_page"
      :totalRecords="categories.total"
      :rowsPerPageOptions="[5, 10, 20]"
      :lazy="true"
    >
      <Column field="name" header="Name" />
      <Column field="description" header="Description" />
      <Column field="sort_order" header="Sort Order" />
      <Column header="is_active">
        <template #body="slotProps">
          <Tag :value="slotProps.data.is_active ? 'Active' : 'Inactive'" :severity="getSeverity(slotProps.data)" />
        </template>
      </Column>
      <Column field="created_at" header="Created At" />
      <Column field="updated_at" header="Updated At" />
      <Column header="Actions">
        <template #body="{ data }">
          <div class="flex gap-2">
            <Button label="View" class="p-button-sm p-button-outlined" @click="handleView(data.id)" />
            <Button label="Edit" class="p-button-sm p-button-outlined" @click="openEditDialog(data.id)" />
            
            <div>
            <Toast />
            <Button label="Delete" severity="warn" @click="handleDelete(data.id)" />
            </div>
          </div>
        </template>
      </Column>
    </DataTable>

    <!-- Create Dialog -->
    <Dialog
      v-model:visible="visible"
      :modal="true"
      header="Create Category"
      dismissableMask
      style="width: 40vw"
    >
       <create @submit="visible = false" />

    </Dialog>

    <!-- Edit Dialog -->
    <Dialog
      v-model:visible="editVisible"
      :modal="true"
      header="Edit Category"
      dismissableMask
      style="width: 40vw"
    >
       <edit
      v-if="editCategory"
      :category="editCategory"
      :parent-categories="parentCategories"
      @submit="editVisible = false"
    />
    </Dialog>

  </AdminLayout>
</template>
