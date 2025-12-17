<script lang="ts" setup>
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useAttrs } from 'vue'
import { defineEmits, defineProps } from 'vue'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()

// useAttrs is used to bind all attributes passed to the form
const $attrs = useAttrs()

const emit = defineEmits(['submit'])


const submit = (e: Event) => {
  e.preventDefault()
  const formData = new FormData(e.target as HTMLFormElement)
  emit('submit', formData)
}

</script>

<template>

<form 
  data-slot="form"
  :class="cn('flex flex-col gap-5 w-full bg-gray-50 p-8 jus rounded-lg shadow-md', props.class)"
  v-bind="$attrs"
  @submit="submit"

>


  <slot />
</form>

</template>