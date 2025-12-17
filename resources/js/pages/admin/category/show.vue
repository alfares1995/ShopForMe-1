<template>
<AdminLayout>

    <Link :href="route('admin.category.index')">
        <Button label="Back" icon="pi pi-plus" />
    </Link>
  <div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold">Category: {{ category.name }}</h1>

    <div class="border p-4 rounded-md shadow-sm bg-white">
      <h2 class="text-xl font-semibold mb-2">Details</h2>
      <div><strong>Slug:</strong> {{ category.slug }}</div>
      <div><strong>Description:</strong> {{ category.description || 'N/A' }}</div>
      <div><strong>Parent:</strong> {{ category.parent?.name || 'None' }}</div>
      <div><strong>Is Active:</strong> {{ category.is_active ? 'Yes' : 'No' }}</div>
      <div><strong>Sort Order:</strong> {{ category.sort_order }}</div>
      <div><strong>Meta Title:</strong> {{ category.meta_title }}</div>
      <div><strong>Meta Description:</strong> {{ category.meta_description }}</div>
    </div>

    <div class="border p-4 rounded-md shadow-sm bg-white">
      <h2 class="text-xl font-semibold mb-2">Children Categories</h2>
      <ul v-if="category.children.length">
        <li v-for="child in category.children" :key="child.id" class="ml-4 list-disc">
          {{ child.name }}
        </li>
      </ul>
      <p v-else class="text-gray-500">No child categories.</p>
    </div>

    <div class="border p-4 rounded-md shadow-sm bg-white">
      <h2 class="text-xl font-semibold mb-4">Products</h2>
      <div v-if="products.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="product in products.data" :key="product.id" class="p-4 border rounded">
          <h3 class="font-semibold">{{ product.name }}</h3>
          <p class="text-sm text-gray-600">Brand: {{ product.brand?.name || 'N/A' }}</p>
          <img
            v-if="product.images?.[0]?.url"
            :src="product.images[0].url"
            alt="Product Image"
            class="mt-2 w-full h-40 object-cover rounded"
          />
        </div>
      </div>
      <p v-else class="text-gray-500">No products found.</p>

      <Pagination :links="products.links" class="mt-6" />
    </div>
  </div>
</AdminLayout>
</template>

<script lang="ts" setup>
import { defineProps } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
export interface Category {
  id: number;
  name: string;
  slug: string;
  description?: string;
  parent_id?: number;
  parent?: Category | null;
  children: Category[];
  is_active: boolean;
  sort_order: number;
  meta_title?: string;
  meta_description?: string;
}

export interface Product {
  id: number;
  name: string;
  brand?: { id: number; name: string };
  images?: { id: number; url: string }[];
}

export interface Paginated<T> {
  data: T[];
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

const props = defineProps<{
  category: Category;
  products: Paginated<Product>;
}>();
</script>
