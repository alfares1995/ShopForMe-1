<script lang="ts" setup>
import { useCartStore } from '@/stores/cart'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
const cartStore = useCartStore()
cartStore.getItems()

const form = ref({
  email: '',
  first_name: '',
  last_name: '',
  phone: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
  shipping_method_id: '',
  coupon_code: '',
  payment_method: 'stripe',
})

const cart = ref([])
const shippingMethods = ref([])

const cartTotal = computed(() =>
  cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
)

// const loadCart = async () => {
//   let { data } = await axios.get('/api/cart')
//   cart.value = data
// }

// const loadShippingMethods = async () => {
//   let { data } = await axios.get('/api/shipping-methods')
//   shippingMethods.value = data
// }

const submitOrder = async () => {
  try {
    const { data } = await axios.post('/checkout', form.value)
    window.location.href = `/checkout/success/${data.order_id}`
  } catch (error) {
    alert(error.response?.data?.message || 'Checkout failed!')
  }
}

// onMounted(() => {
//   loadCart()
//   loadShippingMethods()
// })

</script>

<template>
<h1>Checkout</h1>
    <div v-if="cartStore.itemCount > 0">
      <div v-for="item in cartStore.items" :key="item.id" class="mb-2">
        <div class="flex justify-between items-center">
          <span>{{ item.name }}</span>
          <span>£{{ item.price * item.quantity }}</span>
          <input type="text" v-model.number="item.quantity" class="w-12 text-center border rounded" @change="cartStore.updateQuantity(item.id, item.quantity)" />
          <span>x{{ item.quantity }}</span>
          <button @click="cartStore.removeItem(item.id)" class="text-red-600 hover:text-red-800">
            Remove
          </button>
        </div>
      </div>
      <h1 class="font-bold mt-4">Total: £{{ cartStore.totalPrice }}</h1>
    </div>
    <div v-else class="text-sm text-gray-500">Your cart is empty.</div>
    <div class="checkout max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <!-- Customer Info -->
    <form @submit.prevent="submitOrder">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
          <label class="block font-medium">First Name</label>
          <input v-model="form.first_name" type="text" class="input" required />
        </div>

        <div>
          <label class="block font-medium">Last Name</label>
          <input v-model="form.last_name" type="text" class="input" required />
        </div>

        <div class="md:col-span-2">
          <label class="block font-medium">Email</label>
          <input v-model="form.email" type="email" class="input" required />
        </div>

        <div class="md:col-span-2">
          <label class="block font-medium">Phone</label>
          <input v-model="form.phone" type="text" class="input" />
        </div>
      </div>

      <!-- Address -->
      <h2 class="text-xl font-semibold mb-4">Shipping Address</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="md:col-span-2">
          <label class="block font-medium">Address Line 1</label>
          <input v-model="form.address_line_1" type="text" class="input" required />
        </div>

        <div class="md:col-span-2">
          <label class="block font-medium">Address Line 2</label>
          <input v-model="form.address_line_2" type="text" class="input" />
        </div>

        <div>
          <label class="block font-medium">City</label>
          <input v-model="form.city" type="text" class="input" required />
        </div>

        <div>
          <label class="block font-medium">State</label>
          <input v-model="form.state" type="text" class="input" />
        </div>

        <div>
          <label class="block font-medium">Postal Code</label>
          <input v-model="form.postal_code" type="text" class="input" required />
        </div>

        <div>
          <label class="block font-medium">Country</label>
          <input v-model="form.country" type="text" class="input" required />
        </div>
      </div>

      <!-- Shipping & Payment -->
      <h2 class="text-xl font-semibold mb-4">Options</h2>
      <div class="mb-6">
        <label class="block font-medium">Shipping Method</label>
        <select v-model="form.shipping_method_id" class="input">
          <option disabled value="">-- Select Shipping Method --</option>
          <option v-for="method in shippingMethods" :key="method.id" :value="method.id">
            {{ method.name }} - £{{ method.price }}
          </option>
        </select>
      </div>

      <div class="mb-6">
        <label class="block font-medium">Coupon Code</label>
        <input v-model="form.coupon_code" type="text" class="input" placeholder="Optional" />
      </div>

      <div class="mb-6">
        <label class="block font-medium">Payment Method</label>
        <select v-model="form.payment_method" class="input" required>
          <option value="stripe">Stripe</option>
          <option value="paypal">PayPal</option>
          <option value="cod">Cash on Delivery</option>
        </select>
      </div>

      <!-- Order Summary -->
      <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
      <div class="border p-4 rounded mb-6">
        <div v-for="item in cart" :key="item.id" class="flex justify-between mb-2">
          <span>{{ item.product.name }} (x{{ item.quantity }})</span>
          <span>£{{ (item.price * item.quantity).toFixed(2) }}</span>
        </div>
        <hr class="my-2" />
        <div class="flex justify-between font-bold">
          <span>Total:</span>
          <span>£{{ cartTotal.toFixed(2) }}</span>
        </div>
      </div>

      <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
        Place Order
      </button>
    </form>
  </div>
</template>