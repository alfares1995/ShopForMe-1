<script setup>
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  orderId: Number,
  orderNumber: String,
})
</script>

<template>
  <div class="max-w-3xl mx-auto py-16 text-center">
    <h1 class="text-3xl font-bold mb-4 text-green-600">🎉 Order Successful!</h1>
    <p class="text-lg mb-2">Thank you for your purchase.</p>
    <p class="text-gray-600 mb-6">Your order number is <strong>{{ orderNumber }}</strong>.</p>
    <a href="/" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700">Back to Home</a>
  </div>
</template>
