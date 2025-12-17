<template>
  <div class="p-8">
    <h1 class="text-3xl font-bold mb-6">Available Products</h1>

    <!-- <div v-if="products.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"> -->
      <div v-for="product in products" :key="product.id" class="bg-white shadow rounded-lg p-4">
        <h2 class="text-xl font-semibold">{{ product.name }}</h2>
        <p class="text-gray-600 mt-2">{{ product.description }}</p>
        <p class="text-green-600 mt-4 font-bold">Price: ${{ product.price }}</p>
      </div>
    </div>

    #
  
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { usePage,Link } from '@inertiajs/vue3'
//Props from Inertia
const props = defineProps<{
  products: any,
}>()
</script>

<style scoped>
/* Optional custom styles */
</style>
