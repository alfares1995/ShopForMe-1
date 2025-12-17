<template>
  <div class="flex items-center justify-between border p-4 rounded-lg">
    <div class="flex items-center space-x-4">
      <img
        v-if="item.product.image"
        :src="item.product.image"
        alt="Product"
        class="w-16 h-16 object-cover rounded"
      />
      <div>
        <h2 class="font-semibold">{{ item.product.name }}</h2>
        <p class="text-gray-500">${{ item.product.price.toFixed(2) }}</p>
      </div>
    </div>

    <div class="flex items-center space-x-2">
      <input
        type="number"
        min="1"
        :value="item.quantity"
        @change="onQuantityChange"
        class="w-16 border rounded px-2 py-1"
      />
      <button
        @click="$emit('remove', item.product.id)"
        class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600"
      >
        Remove
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from "vue";
import type { CartItem as CartItemType } from "@/stores/cart";

const props = defineProps<{ item: CartItemType }>();
const emit = defineEmits<{
  (e: "update", productId: number, quantity: number): void;
  (e: "remove", productId: number): void;
}>();

function onQuantityChange(e: Event) {
  const target = e.target as HTMLInputElement;
  const qty = parseInt(target.value);
  if (qty > 0) {
    emit("update", props.item.product.id, qty);
  }
}
</script>
