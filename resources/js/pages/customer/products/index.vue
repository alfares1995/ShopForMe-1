<!-- <script lang="ts" setup>
import { ref } from 'vue';
import { defineProps } from 'vue'

interface Product {
  id: number
  [key: string]: any
  name: string
  sku: string
  price: number
  compare_price?: number | null
  cost_price?: number | null
  stock_quantity: number
  min_stock_level?: number
  track_stock: boolean
  weight?: number | string
  dimensions?: string
  brand_id?: number | string
  short_description: string
  description: string
  meta_title?: string
  meta_description?: string
  is_active: boolean
  is_featured: boolean
  is_digital: boolean
  category_ids: Array<number | string>
  images: Array<{
    id: number
    image_path: string
    alt_text?: string
  }>
  attributes: Record<string, any>
}

const props = defineProps<{
  featuredProducts: any
}>();

</script>
<template>
  <div class="p-6 max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Products</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div
        v-for="product in featuredProducts"
        :key="product.id"
        class="border rounded-lg p-4 flex flex-col"
      >
        <img
          :src="product.images && product.images.length > 0 ? product.images[0]?.image_path : 'https://via.placeholder.com/150'"
          :alt="product.name"
          class="w-full h-48 object-cover mb-4 rounded"
        />
        <h2 class="text-lg font-semibold mb-2">{{ product.name }}</h2>
        <p class="text-gray-600 mb-4">${{ product.price }}</p>
        <button
          class="mt-auto bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
        >
          Add to Cart
        </button>
      </div>
    </div>
  </div>
</template> -->
////////////////////////////////////////////
<script setup lang="ts">
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3'

// TS interfaces
interface Product {
  id: number
  [key: string]: any
  name: string
  sku: string
  price: number
  compare_price?: number | null
  cost_price?: number | null
  stock_quantity: number
  min_stock_level?: number
  track_stock: boolean
  weight?: number | string
  dimensions?: string
  brand_id?: number | string
  brand?: {
    id: number
    name: string
  }
  short_description: string
  description: string
  meta_title?: string
  meta_description?: string
  is_active: boolean
  is_featured: boolean
  is_digital: boolean
  category_ids: Array<number | string>
  images: Array<{
    id: number
    image_path: string
    alt_text?: string
  }>
  attributes: Record<string, any>
}
interface PaginationLink {
  url: string | null
  label: string
  active: boolean
}

interface Paginated<T> {
  data: T[]
  links: PaginationLink[]
  meta: {
    current_page: number
    last_page: number
    total: number
  }
}

const props = defineProps<{
  products: Paginated<Product>
}>()

const getImageUrl = (path: any) => {
  return `/storage/${path}`
}

</script>

<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold">Products</h1>

    <!-- Product Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
      <div
        v-for="product in props.products.data"
        :key="product.id"
        class="border rounded-lg p-4 shadow-sm"
      >
        <img
                v-if="product.images?.length > 0 && product.images[0]?.image_path"
                :src="getImageUrl(product.images[0].image_path)"
                :alt="product.images[0].alt_text || product.name"
                class="w-full h-full object-cover rounded-xl"
                
              />
        <h2 class="text-lg font-semibold">{{ product.name }}</h2>
        <p class="text-gray-500 text-sm">{{ product.brand?.name }}</p>
        <p class="text-green-600 font-bold mt-1">${{ product.price }}</p>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center space-x-2 mt-6">
      <Link
        v-for="link in props.products.links"
        :key="link.label"
        :href="link.url || '#'"
        v-html="link.label"
        class="px-3 py-1 border rounded"
        :class="{
          'bg-gray-200 font-bold': link.active,
          'text-gray-400 pointer-events-none': !link.url
        }"
      />
    </div>
  </div>
</template>
