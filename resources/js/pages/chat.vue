<template>
  <div>
    <div v-for="message in messages" :key="message.id">
      <strong>{{ message.user.name }}:</strong> {{ message.content }}
    </div>
    <form @submit.prevent="sendMessage">
      <input v-model="form.content" type="text" placeholder="Message..." />
      <button type="submit">Send</button>
    </form>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { defineProps } from 'vue'
import { configureEcho } from "@laravel/echo-vue";
import Echo from 'laravel-echo';
declare global {
  interface Window {
    Echo: any
  }
}

const props = defineProps<{ messages: any[] }>()

const messages = ref(props.messages)
const form = useForm({ content: '' })

const sendMessage = () => {
  form.post('/messages', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
    },
  })
}

onMounted(() => {
  window.Echo.channel('chat').listen('.message.sent', (e: any) => {
    messages.value.push(e.message)
  })
})
</script>
