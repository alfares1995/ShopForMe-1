<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-blue-600 px-6 py-4">
          <h1 class="text-2xl font-bold text-white">Complete Your Payment</h1>
          <p class="mt-1 text-blue-100">Secure checkout powered by Stripe</p>
        </div>

        <div class="p-6">
          <div v-if="loading" class="text-center py-12">
            <svg class="animate-spin h-12 w-12 mx-auto text-blue-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-4 text-gray-600">Loading checkout...</p>
          </div>

          <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd"/>
              </svg>
              <p class="text-red-800">{{ error }}</p>
            </div>
            <button
              @click="retryPayment"
              class="mt-3 w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition"
            >
              Try Again
            </button>
          </div>

          <!-- Embedded Stripe Checkout -->
          <div ref="checkoutElement" class="min-h-[500px]"></div>

          <!-- Cancel Button -->
          <div class="mt-6 text-center">
            <a
              :href="route('checkout.index')"
              class="text-gray-600 hover:text-gray-900 underline"
            >
              ← Return to checkout
            </a>
          </div>
        </div>
      </div>

      <!-- Security Badge -->
      <div class="mt-6 text-center">
        <div class="inline-flex items-center gap-2 text-sm text-gray-600">
          <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          <span>Secured by Stripe - Your payment information is encrypted</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { loadStripe } from '@stripe/stripe-js';

const props = defineProps({
  sessionId: String,
  stripeKey: String,
  orderId: Number,
});

const loading = ref(true);
const error = ref('');
const checkoutElement = ref(null);
let stripe = null;

const initializeStripeCheckout = async () => {
  try {
    loading.value = true;
    error.value = '';

    stripe = await loadStripe(props.stripeKey);
    
    if (!stripe) {
      throw new Error('Failed to load Stripe');
    }

    // Initialize embedded checkout
    const checkout = await stripe.initEmbeddedCheckout({
      clientSecret: props.sessionId,
    });

    // Mount the checkout
    if (checkoutElement.value) {
      checkout.mount(checkoutElement.value);
    }

    loading.value = false;
  } catch (e) {
    error.value = e.message || 'An error occurred while loading checkout.';
    loading.value = false;
    console.error('Stripe initialization error:', e);
  }
};

const retryPayment = () => {
  initializeStripeCheckout();
};

onMounted(() => {
  initializeStripeCheckout();
});
</script>