<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

interface AddToCartProps {
  productId: number;
  size?: "sm" | "md" | "lg";
  label?: string;
}


const props = withDefaults(defineProps<AddToCartProps>(), {
  size: "md",
  label: "Add to cart",
});

const loading = ref(false);
const success = ref(false);
let timeoutId: number | undefined;

function addToCart() {
  if (loading.value) return;
  loading.value = true;

  router.post(
    route("cart.add", { product: props.productId }),
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        success.value = true;
        clearTimeout(timeoutId);
        timeoutId = window.setTimeout(() => (success.value = false), 1400);
      },
      onFinish: () => {
        loading.value = false;
      },
    }
  );
}

const sizeClasses = {
  sm: "px-3 py-1.5 text-sm",
  md: "px-4 py-2",
  lg: "px-5 py-3 text-lg",
};
</script>

<template>
  <button
    :disabled="loading"
    @click="addToCart"
    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white font-medium shadow
           hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed transition"
    :class="sizeClasses[size]"
  >
    <span v-if="loading" class="animate-pulse">Adding…</span>
    <span v-else-if="success">Added ✓</span>
    <span v-else>{{ label }}</span>
    <slot />
  </button>
</template>
