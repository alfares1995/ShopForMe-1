<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { usePage,Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
// import { route } from '@inertiajs/vue3'

// import Pagination from '@/Components/Pagination.vue' // Create or reuse a pagination component

//Props from Inertia
const props = defineProps<{
  products: any,
  categories: any[],
  brands: any[],
  filters: {
    search?: string,
    category_id?: string|number,
    brand_id?: string|number,
    stock_status?: string,
    is_active?: string|boolean
  }
}>()

const page = usePage()

// // Reactive users and filters
const products = ref<{ data: any[]; [key: string]: any }>(page.props.products as { data: any[]; [key: string]: any })
const categories = ref<Array<{ id: number|string; name: string }>>(page.props.categories as Array<{ id: number|string; name: string }>)
const brands = ref<Array<{ id: number|string; name: string }>>(page.props.brands as Array<{ id: number|string; name: string }>)

// Reactive filters
const filters = ref({ 
    search: props.filters.search || '',
    category_id: props.filters.category_id || '',
    brand_id: props.filters.brand_id || '',
    stock_status: props.filters.stock_status || '',
    is_active: props.filters.is_active || ''
 })

// Watch for Inertia updates to sync data
watch(
  () => page.props.products,
  (newProducts) => {
    products.value = newProducts as { data: any[]; [key: string]: any }
  }
)

// Submit form
// const form = useForm(filters.value)

const submit = () => {
  router.get(route('admin.product.index'), filters.value, {
    preserveState: true,
    //preserveScroll: true,
    replace: true
  })
}

// Utility function to get image URL
const getImageUrl = (path: any) => {
    return `/storage/${path}`
}

// Handle delete action
const handleDelete = (id: number) => {
  if (confirm(`Are you sure you want to delete this product?`)) {
    router.delete(route('admin.product.destroy', { id }), {
      preserveScroll: true,
      onSuccess: () => {
        console.log('Product deleted successfully');
      },
      onError: () => {
        console.log('Failed to delete product');
      }
    });
  }
}

// Utility class for status
const statusClass = (isActive: boolean) =>
  isActive ? 'text-green-600 font-semibold' : 'text-red-500 font-semibold'
</script>



<template>
<AdminLayout>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Product Management</h1>
     <Link
        :href="route('admin.product.create')"
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Create Product
      </Link>

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
      <input v-model="filters.search" @input="submit" type="text" placeholder="Search by name or SKU" class="input" />

      <select v-model="filters.category_id" @change="submit" class="input">
        <option value="">All Categories</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">
          {{ category.name }}
        </option>
      </select>

      <select v-model="filters.brand_id" @change="submit" class="input">
        <option value="">All Brands</option>
        <option v-for="brand in brands" :key="brand.id" :value="brand.id">
          {{ brand.name }}
        </option>
      </select>

      <select v-model="filters.stock_status" @change="submit" class="input">
        <option value="">All Stock Status</option>
        <option value="in_stock">In Stock</option>
        <option value="out_of_stock">Out of Stock</option>
        <option value="on_backorder">On Backorder</option>
      </select>

      <select v-model="filters.is_active" @change="submit" class="input">
        <option value="">All Status</option>
        <option :value="true">Active</option>
        <option :value="false">Inactive</option>
      </select>
    </div>
  </div>
  <!-- Products Table -->
   <DataTable :value="products.data" class="p-6">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold">Products</h2>
        <p class="text-sm text-gray-500">Total: {{ products.data.length }}</p>
      </div>
    </template>

    <Column field="name" header="Name" />
    <Column field="sku" header="SKU" />
    <Column field="price" header="Price" />
    <Column field="brand.name" header="Brand" />
    <Column field="categories" header="Categories">
      <template #body="{ data }">
        {{ data.categories.map((cat: { name: string }) => cat.name).join(', ') }}
      </template>
    </Column>
   
    <Column header="Images">
        <template #body="slotProps">
            <img v-if="slotProps.data.images && slotProps.data.images.length > 0" :src="`${getImageUrl(slotProps.data.images[0].image_path)}`" :alt="slotProps.data.images[0].alt" class="w-24 rounded" />
        </template>
    </Column>
    <Column field="is_active" header="Status">
      <template #body="{ data }">
        <span :class="statusClass(data.is_active)">{{ data.is_active ? 'Active' : 'Inactive' }}</span>
      </template>
    </Column>
    <Column header="Actions">
      <template #body="{ data }">
        <Link :href="route('admin.product.show', { id: data.id })" class="text-blue-600 hover:underline">View</Link>
        <Link :href="route('admin.product.edit', { id: data.id })" class="text-blue-600 hover:underline">Edit</Link>
        <button @click="handleDelete(data.id)" class="text-red-600 hover:underline ml-2">Delete</button>
      </template>
    </Column>
  </DataTable>



</AdminLayout>
</template>



