<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

interface Product {
  id: number
  name: string
  sku: string
  slug: string
  short_description: string
  description: string
  price: number
  compare_price?: number
  cost_price?: number
  stock_quantity: number
  stock_status: string
  track_stock: boolean
  weight?: number
  dimensions?: string
  brand?: { name: string }
  categories: Array<{ id: number; name: string }>
  is_featured: boolean
  is_active: boolean
  is_digital: boolean
  images: Array<{ id: number; image_path: string; alt_text: string; is_primary: boolean }>
  attribute_values: Array<{ id: number; attribute: { name: string }; value: string }>
  reviews?: Array<{ id: number; user: { name: string }; comment: string }>
}

const page = usePage()
const product = computed<Product>(() => page.props.product as Product)

</script>
<template>
<AdminLayout>
  <div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold">Product Details</h1>
      <Link :href="route('admin.product.index')" class="btn">Back to Products</Link>
    </div>

    <!-- Product Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-4 rounded shadow">
      <div>
        <h2 class="text-lg font-semibold mb-2">{{ product.name }}</h2>
        <p class="text-sm text-gray-600">SKU: {{ product.sku }}</p>
        <p class="text-sm text-gray-600">Slug: {{ product.slug }}</p>
        <p class="mt-2">{{ product.short_description }}</p>
        <p class="mt-4 text-sm text-gray-800">{{ product.description }}</p>

        <div class="mt-4 space-y-1">
          <div><strong>Price:</strong> £{{ product.price }}</div>
          <div v-if="product.compare_price"><strong>Compare Price:</strong> £{{ product.compare_price }}</div>
          <div v-if="product.cost_price"><strong>Cost Price:</strong> £{{ product.cost_price }}</div>
          <div><strong>Stock:</strong> {{ product.stock_quantity }} ({{ product.stock_status }})</div>
          <div><strong>Track Stock:</strong> {{ product.track_stock ? 'Yes' : 'No' }}</div>
          <div v-if="product.weight"><strong>Weight:</strong> {{ product.weight }} kg</div>
          <div v-if="product.dimensions"><strong>Dimensions:</strong> {{ product.dimensions }}</div>
        </div>

        <div class="mt-4 space-y-1">
          <div><strong>Brand:</strong> {{ product.brand?.name || '—' }}</div>
          <div><strong>Categories:</strong>
            <span v-if="product.categories.length">
              <span v-for="(cat, idx) in product.categories" :key="cat.id">
                {{ cat.name }}<span v-if="idx < product.categories.length - 1">, </span>
              </span>
            </span>
            <span v-else>—</span>
          </div>
        </div>

        <div class="mt-4 space-y-1">
          <div><strong>Featured:</strong> {{ product.is_featured ? 'Yes' : 'No' }}</div>
          <div><strong>Active:</strong> {{ product.is_active ? 'Yes' : 'No' }}</div>
          <div><strong>Digital Product:</strong> {{ product.is_digital ? 'Yes' : 'No' }}</div>
        </div>
      </div>

      <!-- Images -->
      <div class="grid grid-cols-2 gap-4">
        <div v-for="img in product.images" :key="img.id" class="relative">
          <img :src="`/storage/${img.image_path}`" :alt="img.alt_text" class="rounded shadow object-cover w-full h-40" />
          <span v-if="img.is_primary" class="absolute top-1 left-1 bg-green-600 text-white text-xs px-2 py-1 rounded">
            Primary
          </span>
        </div>
      </div>
    </div>

    <!-- Attributes -->
    <div v-if="product.attribute_values.length" class="bg-white p-4 rounded shadow">
      <h2 class="text-lg font-semibold mb-2">Attributes</h2>
      <ul class="list-disc list-inside space-y-1">
        <li v-for="attr in product.attribute_values" :key="attr.id">
          <strong>{{ attr.attribute.name }}:</strong> {{ attr.value }}
        </li>
      </ul>
    </div>

    <!-- Reviews (optional) -->
    <div v-if="product.reviews?.length" class="bg-white p-4 rounded shadow">
      <h2 class="text-lg font-semibold mb-2">Approved Reviews</h2>
      <div v-for="review in product.reviews" :key="review.id" class="border-t pt-2 mt-2">
        <div class="text-sm text-gray-600">By {{ review.user.name }}</div>
        <div>{{ review.comment }}</div>
      </div>
    </div>
  </div>
</AdminLayout>
</template>