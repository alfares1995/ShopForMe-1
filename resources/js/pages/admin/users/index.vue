<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import PlaceholderPattern from '../../../components/PlaceholderPattern.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import { ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Users',
        href: '/admin/users',
    },
];
const page = usePage()

// Reactive users and filters
const users = ref(page.props.users)
const filters = ref({
  search: page.props.filters.search || '',
  role: page.props.filters.role || '',
  status: page.props.filters.status || ''
})

// Watch for Inertia updates to sync data
watch(
  () => page.props.users,
  (newUsers) => {
    users.value = newUsers
  }
)

// Re-fetch with current filters
const submitFilters = () => {
  router.get(route('admin.users.index'), filters.value, {
    preserveState: true,
    replace: true
  })
}

const changePage = (pageNumber: number) => {
  router.get(route('admin.users.index'), { ...filters.value, page: pageNumber }, {
    preserveState: true,
    replace: true
  })
}
</script>


<template>
    <AdminLayout>
        <Head title="Users" />
        
        
          <div class="p-6">
    <h1 class="text-2xl font-semibold mb-4">Users</h1>
    <div class="mb-4">
      <Link
        :href="route('admin.users.create')"
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Create User
      </Link>

    <div class="mb-4 flex flex-wrap gap-2">
      <input
        v-model="filters.search"
        type="text"
        placeholder="Search..."
        class="border px-3 py-2 rounded"
        @input="submitFilters"
      />

      <select v-model="filters.role" @change="submitFilters" class="border px-3 py-2 rounded">
        <option value="">All Roles</option>
        <option value="admin">Admin</option>
        <option value="user">Customer</option>
      </select>

      <select v-model="filters.status" @change="submitFilters" class="border px-3 py-2 rounded">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="suspended">Suspended</option>
      </select>
    </div>

    <table class="w-full border-collapse table-auto">
      <thead>
        <tr class="bg-gray-100">
          <th class="text-left p-2 border">Name</th>
          <th class="text-left p-2 border">Email</th>
          <th class="text-left p-2 border">Role</th>
          <th class="text-left p-2 border">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
          <td class="p-2 border">{{ user.name }} </td>
          <td class="p-2 border">{{ user.email }}</td>
          <td class="p-2 border">{{ user.role }}</td>
          <td class="p-2 border">{{ user.status }}</td>
        </tr>
      </tbody>
    </table>

   
  </div>
  </div>
        
    </AdminLayout>

</template>


