// import { defineStore } from 'pinia'
// import { ref, computed } from 'vue'
// import{router} from '@inertiajs/vue3'

// interface CartItem {
//   id: number
//   name: string
//   price: number
//   quantity: number
// }

// export const useCartStore = defineStore('cart', () => {
//   const items = ref<CartItem[]>([])

// //   const addItem = (newItem: CartItem) => {
// //     const existing = items.value.find(item => item.id === newItem.id)
// //     if (existing) {
// //       existing.quantity += newItem.quantity
// //     } else {
// //       items.value.push({ ...newItem })
// //     }
// //   }
// const addItem = (newItem: CartItem) => {
//   const existing = items.value.find(item => item.id === newItem.id)
//   if (existing) {
//     existing.quantity += newItem.quantity
//   } else {
//     items.value.push({ ...newItem })
//   }
//   router.post(`/cart/add/${newItem.id}`, {}, { preserveScroll: true })
// }


//   const updateQuantity = (itemId: number, quantity: number) => {
//     const item = items.value.find(item => item.id === itemId)
//     if (item) item.quantity = quantity
//   }

// //   const removeItem = (itemId: number) => {
// //     items.value = items.value.filter(item => item.id !== itemId)
// //   }
// const removeItem = (itemId: number) => {
//   items.value = items.value.filter(item => item.id !== itemId)
//   router.delete(`/cart/remove/${itemId}`, {
//     preserveScroll: true,
//   })
// }

//   const clearCart = () => {
//     items.value = []
//   }

//   const itemCount = computed(() => items.value.length)

//   const totalPrice = computed(() =>
//     items.value.reduce((total, item) => total + item.price * item.quantity, 0)
//   )

//   const totalQuantity = computed(() =>
//     items.value.reduce((total, item) => total + item.quantity, 0)
//   )

//   const isEmpty = computed(() => items.value.length === 0)

//   const getItems = () => items.value

//   return {
//     items,
//     addItem,
//     updateQuantity,
//     removeItem,
//     clearCart,
//     itemCount,
//     totalPrice,
//     totalQuantity,
//     isEmpty,
//     getItems
//   }
// }, {
//   persist: true // Optional
// })
////////////////////////////////////////////////////////////////////////////////////
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

interface CartItem {
  id: number
  name: string
  price: number
  quantity: number
}

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([])

  // --- Add item ---
  const addItem = (newItem: CartItem) => {
    // Update local store instantly
    const existing = items.value.find(item => item.id === newItem.id)
    if (existing) {
      existing.quantity += newItem.quantity
    } else {
      items.value.push({ ...newItem })
    }

    // Sync with Laravel backend
    router.post(`/cart/add/${newItem.id}`, {}, {
      preserveScroll: true,
      onError: (err) => {
        console.error('Add to cart failed:', err)
      }
    })
  }

  // --- Update quantity ---
  const updateQuantity = (itemId: number, quantity: number) => {
    if (quantity <= 0) {
      removeItem(itemId)
      return
    }

    const item = items.value.find(item => item.id === itemId)
    if (item) item.quantity = quantity

    router.put(`/cart/update/${itemId}`, { quantity }, {
      preserveScroll: true,
      onError: (err) => {
        console.error('Update quantity failed:', err)
      }
    })
  }

  // --- Remove item ---
  const removeItem = (itemId: number) => {
    items.value = items.value.filter(item => item.id !== itemId)

    router.delete(`/cart/remove/${itemId}`, {
      preserveScroll: true,
      onError: (err) => {
        console.error('Remove from cart failed:', err)
      }
    })
  }

  // --- Clear cart ---
  const clearCart = () => {
    items.value = []

    router.delete(`/cart/clear`, {
      preserveScroll: true,
      onError: (err) => {
        console.error('Clear cart failed:', err)
      }
    })
  }

  // --- Computed values ---
  const itemCount = computed(() =>
    items.value.reduce((total, item) => total + item.quantity, 0)
  )

  const totalPrice = computed(() =>
    items.value.reduce((total, item) => total + item.price * item.quantity, 0)
  )

  const isEmpty = computed(() => items.value.length === 0)

  const getItems = async () => {
  try {
    const response = await axios.get('/cart/items')
    items.value = response.data
    return items.value
  } catch (err) {
    console.error('Failed to fetch cart items:', err)
    return []
  }
}
const hasItem = computed(() => items.value.length > 0)

  const getItemQuantity = (itemId: number) => {
    const item = items.value.find(item => item.id === itemId)
    return item ? item.quantity : 0
  }

  const isLoading = computed(() => items.value.length === 0)
  const lastSyncError = ref<string | null>(null)
  const clearError = () => {
    lastSyncError.value = null
  }

  const initializeCart = async () => {
    try {
      const response = await axios.get('/cart/items')
      items.value = response.data
    } catch (error) {
      console.error('Failed to initialize cart:', error)
    }
  }

  return {
    items,
    addItem,
    updateQuantity,
    removeItem,
    clearCart,
    itemCount,
    totalPrice,
    isEmpty,
    getItems,
    hasItem,
    getItemQuantity,
    isLoading,
    lastSyncError,
    clearError,
    initializeCart
  }
}, {
  // persist: true
})
//////////////////////////////////////////////////////////////////////////////
