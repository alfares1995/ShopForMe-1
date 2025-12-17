<script lang="ts" setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import { ref } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'

const props = defineProps({
  roles: Array,
})
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '',
  phone: '',
  date_of_birth: '',
  status: '',
})
const errors = ref<Record<string, string>>({})
const page = usePage()
const submit = () => {
  form.post(route('admin.users.store'), {
    onError: (error) => {
      errors.value = error
    },
    onSuccess: (): void => {
      form.reset()
      errors.value = {}
    },
  })
}
</script>
<template>
<AdminLayout>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Create User</h1>
    <form @submit.prevent="submit">
      <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input
          v-model="form.name"
          type="text"
          id="name"
          autocomplete="name"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
          required> 
        <span v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</span>
      </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
            v-model="form.email"
            type="email"
            id="email"
            autocomplete="email"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required>
            <span v-if="errors.email" class="text-red-500 text-sm">{{ errors.email }}</span>
        </div>
      <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input
          v-model="form.password"
          type="password"
          id="password"
          autocomplete="new-password"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
          required>
        <span v-if="errors.password" class="text-red-500 text-sm">{{ errors.password }}</span>
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input
            v-model="form.password_confirmation"
            type="password"
            autocomplete="new-password"
            id="password_confirmation"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required>
            <span v-if="errors.password_confirmation" class="text-red-500 text-sm">{{ errors.password_confirmation }}</span>
        </div>
      <div class="mb-4">
        <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
        <select
          v-model="form.role"
          id="role_id"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
          required>
          <option value="">Select Role</option>
          
          <option value="admin">Admin</option>
          <option value="user">Customer</option>
        </select>
        <span v-if="errors.role_id" class="text-red-500 text-sm">{{ errors.role_id }}</span>    
        </div>
        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
            <input
            v-model="form.phone"
            type="text"
            id="phone"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <span v-if="errors.phone" class="text-red-500 text-sm">{{ errors.phone }}</span>
        </div>
        <div class="mb-4">
            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
            <input
            v-model="form.date_of_birth"
            type="date"
            id="date_of_birth"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <span v-if="errors.date_of_birth" class="text-red-500 text-sm">{{ errors.date_of_birth }}</span>
        </div>
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select
              v-model="form.status"
              id="status"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required>
              <option value="">Select Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="suspended">Suspended</option>
            </select>
            <span v-if="errors.status" class="text-red-500 text-sm">{{ errors.status }}</span>
        </div>
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          Create User
        </button>
    </form>
    
    </div>
</AdminLayout>
</template>