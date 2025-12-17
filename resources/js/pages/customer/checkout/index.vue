<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        <p class="mt-2 text-gray-600">Complete your purchase</p>
      </div>

      <!-- Alert for errors -->
      <div v-if="form.errors.checkout || form.errors.cart" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd"/>
          </svg>
          <p class="text-red-800">{{ form.errors.checkout || form.errors.cart }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Form -->
        <div class="lg:col-span-2">
          <form @submit.prevent="submitOrder" class="space-y-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact Information</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    First Name *
                  </label>
                  <input
                    v-model="form.first_name"
                    type="text"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.first_name }"
                  />
                  <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.first_name }}
                  </p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Last Name *
                  </label>
                  <input
                    v-model="form.last_name"
                    type="text"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.last_name }"
                  />
                  <p v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.last_name }}
                  </p>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email *
                  </label>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.email }"
                  />
                  <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                    {{ form.errors.email }}
                  </p>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Phone
                  </label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
              </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow-sm p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Shipping Address</h2>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Address Line 1 *
                  </label>
                  <input
                    v-model="form.address_line_1"
                    type="text"
                    required
                    placeholder="Street address"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.address_line_1 }"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Address Line 2
                  </label>
                  <input
                    v-model="form.address_line_2"
                    type="text"
                    placeholder="Apartment, suite, etc. (optional)"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      City *
                    </label>
                    <input
                      v-model="form.city"
                      type="text"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      :class="{ 'border-red-500': form.errors.city }"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      State/County
                    </label>
                    <input
                      v-model="form.state"
                      type="text"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Postal Code *
                    </label>
                    <input
                      v-model="form.postal_code"
                      type="text"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      :class="{ 'border-red-500': form.errors.postal_code }"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Country *
                    </label>
                    <select
                      v-model="form.country"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                      <option value="GB">United Kingdom</option>
                      <option value="US">United States</option>
                      <option value="CA">Canada</option>
                      <option value="IE">Ireland</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white rounded-lg shadow-sm p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Method</h2>
              
              <div class="space-y-3">
                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                  <input
                    v-model="form.payment_method"
                    type="radio"
                    value="stripe"
                    class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                  />
                  <div class="ml-3 flex-1">
                    <div class="font-medium text-gray-900">Credit/Debit Card</div>
                    <div class="text-sm text-gray-500">Pay securely with Stripe</div>
                  </div>
                  <div class="flex gap-2">
                    <img src="/images/visa.svg" alt="Visa" class="h-8" />
                    <img src="/images/mastercard.svg" alt="Mastercard" class="h-8" />
                  </div>
                </label>

                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                  <input
                    v-model="form.payment_method"
                    type="radio"
                    value="cod"
                    class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                  />
                  <div class="ml-3">
                    <div class="font-medium text-gray-900">Cash on Delivery</div>
                    <div class="text-sm text-gray-500">Pay when you receive</div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="form.processing || cartItems.length === 0"
              class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition flex items-center justify-center"
            >
              <svg v-if="form.processing" class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ form.processing ? 'Processing...' : 'Place Order' }}
            </button>
          </form>
        </div>

        <!-- Right Column - Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm p-6 sticky top-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
            
            <!-- Cart Items -->
            <div class="space-y-4 mb-6">
              <div
                v-for="item in cartItems"
                :key="item.id"
                class="flex gap-4"
              >
                <img
                  :src="item.product?.image || '/images/placeholder.png'"
                  :alt="item.product?.name"
                  class="w-16 h-16 object-cover rounded"
                />
                <div class="flex-1">
                  <h3 class="font-medium text-gray-900 text-sm">
                    {{ item.product?.name }}
                  </h3>
                  <p class="text-sm text-gray-500">Qty: {{ item.quantity }}</p>
                </div>
                <div class="text-right">
                  <p class="font-medium text-gray-900">
                    £{{ (item.product?.price * item.quantity).toFixed(2) }}
                  </p>
                </div>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-4 space-y-2">
              <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span>£{{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-gray-600">
                <span>Shipping</span>
                <span>£{{ shipping.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-gray-600">
                <span>Tax (20%)</span>
                <span>£{{ tax.toFixed(2) }}</span>
              </div>
              <div class="border-t border-gray-200 pt-2 flex justify-between text-lg font-bold text-gray-900">
                <span>Total</span>
                <span>£{{ total.toFixed(2) }}</span>
              </div>
            </div>

            <!-- Free Shipping Banner -->
            <div v-if="subtotal < 50" class="mt-4 p-3 bg-blue-50 rounded-lg">
              <p class="text-sm text-blue-800">
                Add £{{ (50 - subtotal).toFixed(2) }} more for free shipping!
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  cartItems: Array,
  stripeKey: String,
});

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state: '',
  postal_code: '',
  country: 'GB',
  payment_method: 'stripe',
});

const subtotal = computed(() => {
  return props.cartItems.reduce((sum, item) => {
    return sum + (item.product?.price || 0) * item.quantity;
  }, 0);
});

const shipping = computed(() => {
  return subtotal.value >= 50 ? 0 : 4.99;
});

const tax = computed(() => {
  return subtotal.value * 0.20;
});

const total = computed(() => {
  return subtotal.value + shipping.value + tax.value;
});

const submitOrder = () => {
  form.post(route('checkout.store'), {
    preserveScroll: true,
  });
};
</script>