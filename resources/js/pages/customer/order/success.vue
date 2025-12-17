<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'

interface FlashProps {
  order_number?: string
}

import type { PageProps as InertiaPageProps } from '@inertiajs/core'

interface PageProps extends InertiaPageProps {
  flash?: FlashProps
}

const page = usePage<PageProps>()
const orderNumber = page.props.flash?.order_number
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
      <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h1>
      <p class="text-gray-600 mb-6">Thank you for your order.</p>
      <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-500">Order Number</p>
        <p class="text-xl font-semibold text-gray-900">{{ orderNumber }}</p>
      </div>
      <a href="/" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700">
        Continue Shopping
      </a>
    </div>
  </div>
</template>