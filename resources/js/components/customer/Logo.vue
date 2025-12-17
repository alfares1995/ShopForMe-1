<script lang="ts" setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
// Define a type for the logo component
interface LogoProps {
  text: string
}
// Create a reactive property for the logo text
const logoText = ref<LogoProps>({ text: 'ShopForme' })
// Reactive state for the logo color
const logoColor = ref('text-white')
// Reactive state for the logo highlight color
const highlightColor = ref('text-blue-600 dark:text-sky-400')
// Reactive state for the logo size
const logoSize = ref('text-2xl')
// Reactive state for the logo font weight
const fontWeight = ref('font-bold')
// Reactive state for font family
const fontFamily = ref('font-sans')


</script>
<template>
  <div class="flex items-center">
    <div :class="[fontFamily,logoSize, fontWeight, logoColor]">
      <span :class="highlightColor">Shop</span>Forme
    </div>
  </div>
</template>