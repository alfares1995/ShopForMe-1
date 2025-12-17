<script setup lang="ts">
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";

interface Product {
  id: number;
  name: string;
  price: number;
}

interface CartItem {
  product: Product;
  quantity: number;
}

const props = defineProps<{
  cart: Record<number, CartItem>;
}>();

const isOpen = ref(false);

function removeItem(productId: number) {
  router.delete(route("cart.remove", { product: productId }), {
    preserveScroll: true,
  });
}

const cartItems = computed(() => Object.values(props.cart));

const cartTotal = computed(() =>
  cartItems.value.reduce(
    (sum, item) => sum + item.product.price * item.quantity,
    0
  )
);

const cartCount = computed(() =>
  cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
);
</script>

<template>
  <div class="relative">
    <!-- Cart Button -->
    <button
      @click="isOpen = !isOpen"
      class="relative p-2 rounded-lg bg-gray-100 hover:bg-gray-200"
    >
      🛒
      <span
        v-if="cartCount > 0"
        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1 rounded-full"
      >
        {{ cartCount }}
      </span>
    </button>

    <!-- Dropdown -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-72 bg-white border rounded-xl shadow-lg z-50"
    >
      <div class="p-4">
        <h3 class="font-bold text-lg mb-3">Cart</h3>

        <!-- Empty State -->
        <div v-if="cartItems.length === 0" class="text-gray-500 text-sm">
          Your cart is empty.
        </div>

        <!-- Items -->
        <div v-else class="space-y-3 max-h-60 overflow-y-auto">
          <div
            v-for="item in cartItems"
            :key="item.product.id"
            class="flex justify-between items-center"
          >
            <div>
              <p class="font-medium">{{ item.product.name }}</p>
              <p class="text-xs text-gray-500">
                {{ item.quantity }} × ${{ item.product.price }}
              </p>
            </div>
            <button
              @click="removeItem(item.product.id)"
              class="text-red-500 hover:underline text-sm"
            >
              ✕
            </button>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="p-4 border-t flex justify-between items-center">
        <span class="font-semibold">Total:</span>
        <span class="font-bold">${{ cartTotal.toFixed(2) }}</span>
      </div>

      <!-- Actions -->
      <div class="p-4 flex justify-between">
        <a
          href="/cart"
          class="px-3 py-2 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600"
        >
          View Cart
        </a>
        <a
          href="/checkout"
          class="px-3 py-2 text-sm bg-green-500 text-white rounded-lg hover:bg-green-600"
        >
          Checkout
        </a>
      </div>
    </div>
  </div>
</template>
