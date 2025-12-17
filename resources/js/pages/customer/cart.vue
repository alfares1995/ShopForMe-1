<script lang="ts" setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{ cart: Record<number, any> }>()

const footerLogoText = ref('ShopForMe')

function removeFromCart(productId: number) {
  router.delete(`/cart/remove/${productId}`)
}

const quantity = ref(1)

const checkout = () => {
  router.get('/checkout')
}


</script>

<template>
  <div>
    <h1>Your Cart</h1>
    
    <div v-for="(item, id) in cart" :key="id">
      <h2>{{ item.product.name }}</h2>
      <p>Qty: <input type="number" v-model="item.quantity" min="1" /></p>
      <p>Price: ${{ item.product.price * item.quantity }}</p>
      <button @click="removeFromCart(item.product.id)">Remove</button>
    </div>
  </div>
  <div>
    <button></button>
    <button @click="checkout">Checkout</button>
  </div>
</template>
///////////////////////////////////////////////////////////////////////////
<!-- <template>
  <div class="p-6 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Your Cart</h1>

    <div v-if="items.length === 0" class="text-gray-500">
      Your cart is empty.
    </div>

    <div v-else class="space-y-4">
      <CartItem
        v-for="item in items"
        :key="item.product.id"
        :item="item"
        @update="updateQuantity"
        @remove="removeItem"
      />

      <div class="mt-6 flex justify-between items-center border-t pt-4">
        <span class="text-lg font-semibold">Total:</span>
        <span class="text-xl font-bold">${{ total.toFixed(2) }}</span>
      </div>

      <button
        @click="clearCart"
        class="bg-red-500 text-white px-4 py-2 rounded-lg mt-4 hover:bg-red-600"
      >
        Clear Cart
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCartStore } from "@/stores/cart";
import { computed } from "vue";
import CartItem from "@/components/customer/CartItem.vue";

const cart = useCartStore();

const items = computed(() => cart.items);
const total = computed(() => cart.total);

function updateQuantity(productId: number, quantity: number) {
  cart.update(productId, quantity);
}

function removeItem(productId: number) {
  cart.remove(productId);
}

function clearCart() {
  cart.clear();
}
</script> -->
