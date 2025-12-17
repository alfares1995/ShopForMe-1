<template>
  <div :class="['min-h-screen',backgroundColor]">

    <NavMenu/>
    <!-- Hero Section -->
    <section class="pt-24 pb-20 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        <div class="text-center">
          <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 animate-fade-in">
            Discover
            <span class="text-white bg-clip-text">
              Premium
            </span>
            <br>Shopping
          </h1>
          <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto animate-slide-up">
            Experience the future of e-commerce with our curated collection of premium products, 
            lightning-fast delivery, and unmatched customer service.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center items-center animate-slide-up">
            <button class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-4 rounded-full text-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-2xl">
              Shop Now
            </button>
            <button class="border-2 border-white/30 hover:border-white/50 text-white px-8 py-4 rounded-full text-lg font-semibold transition-all duration-300 backdrop-blur-sm hover:bg-white/10">
              View Catalog
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-pink-900 dark:bg-gray-800">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-white mb-4">Featured Products</h2>
          <p class="text-gray-300 text-lg">Handpicked items just for you</p>
        </div>
        
        <!-- Loading indicator -->
        <div v-if="cart.isLoading" class="fixed top-4 right-4 bg-white/90 backdrop-blur-sm rounded-lg p-3 shadow-lg z-50">
          <div class="flex items-center space-x-2">
            <div class="animate-spin rounded-full h-4 w-4 border-2 border-purple-600 border-t-transparent"></div>
            <span class="text-sm font-medium">{{ loadingMessage }}</span>
          </div>
        </div>

        <!-- Error notification -->
        <div v-if="cart.lastSyncError" class="fixed top-4 right-4 bg-red-500 text-white rounded-lg p-3 shadow-lg z-50 max-w-sm">
          <div class="flex items-center justify-between">
            <span class="text-sm">{{ cart.lastSyncError }}</span>
            <button @click="cart.clearError" class="ml-2 text-white hover:text-gray-200">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Success notification -->
        <div v-if="showSuccessMessage" class="fixed top-4 right-4 bg-green-500 text-white rounded-lg p-3 shadow-lg z-50 max-w-sm">
          <div class="flex items-center justify-between">
            <span class="text-sm">{{ successMessage }}</span>
            <button @click="showSuccessMessage = false" class="ml-2 text-white hover:text-gray-200">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="product in featuredProducts" :key="product.id" 
               class="bg-[#FB8DA0] backdrop-blur-lg rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 transform hover:scale-105 group">
            <div class="aspect-square bg-gradient-to-br from-[#FB8DA0] to-pink-400/20 rounded-xl mb-4 flex items-center justify-center">
              <img
                v-if="product.images?.length > 0 && product.images[0]?.image_path"
                :src="getImageUrl(product.images[0].image_path)"
                :alt="product.images[0].alt_text || product.name"
                class="w-full h-full object-cover rounded-xl"
                
              />
              <div v-else class="w-full h-full bg-gray-300 rounded-xl flex items-center justify-center">
                <span class="text-gray-500 text-4xl">📦</span>
              </div>
            </div>
            
            <h3 class="text-xl font-semibold text-white mb-2">{{ product.name }}</h3>
            <p class="text-gray-300 mb-4 line-clamp-2">{{ product.short_description || product.description }}</p>
            
            <!-- Stock indicator -->
            <div class="mb-3">
              <span v-if="product.stock_quantity > 0" class="text-xs text-green-300">
                {{ product.stock_quantity }} in stock
              </span>
              <span v-else class="text-xs text-red-300">Out of stock</span>
            </div>
            
            <div class="flex justify-between items-center">
              <div class="flex flex-col">
                <span class="text-2xl font-bold text-purple-400">£{{ formatPrice(product.price) }}</span>
                <span v-if="product.compare_price && product.compare_price > product.price" 
                      class="text-sm text-gray-400 line-through">
                  £{{ formatPrice(product.compare_price) }}
                </span>
              </div>

              <div class="flex items-center space-x-2">
                <!-- Quantity in cart indicator -->
                <span v-if="cart.hasItem" class="text-xs bg-purple-600 text-white px-2 py-1 rounded-full">
                  {{ cart.getItemQuantity(product.id) }} in cart
                </span>
                
                <!-- Add to cart button -->
                <button 
                  @click="addToCart(product)" 
                  :disabled="isAddingToCart[product.id] || product.stock_quantity === 0"
                  class="bg-purple-600 hover:bg-purple-700 disabled:bg-gray-500 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg transition-colors duration-200 group-hover:scale-105 transform flex items-center space-x-1"
                >
                  <span v-if="isAddingToCart[product.id]" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                  <span v-else-if="product.stock_quantity === 0">Out of Stock</span>
                  <span v-else>Add to Cart</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-white mb-4">Why Choose ShopElite?</h2>
          <p class="text-gray-300 text-lg">Experience shopping redefined</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="feature in features" :key="feature.id" 
               class="text-center group hover:transform hover:scale-105 transition-all duration-300">
            <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:shadow-2xl group-hover:shadow-purple-500/25">
              <div class="text-white text-2xl">{{ feature.icon }}</div>
            </div>
            <h3 class="text-xl font-semibold text-white mb-4">{{ feature.title }}</h3>
            <p class="text-gray-300">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-[#FB8DA0] to-pink-600/50">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl font-bold text-white mb-4">Stay in the Loop</h2>
        <p class="text-gray-300 text-lg mb-8">Get exclusive deals, new arrivals, and insider updates delivered to your inbox.</p>
        
        <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
          <input 
            v-model="email"
            type="email" 
            placeholder="Enter your email" 
            class="flex-1 px-6 py-4 rounded-full bg-white/10 backdrop-blur-lg border border-white/20 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
          <button 
            @click="subscribeNewsletter"
            class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-4 rounded-full font-semibold transition-all duration-300 transform hover:scale-105"
          >
            Subscribe
          </button>
        </div>
        
        <div v-if="subscriptionMessage" class="mt-4 text-green-400">
          {{ subscriptionMessage }}
        </div>
      </div>
    </section>
    <!-- Footer -->
    <Footer/>
   
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import NavMenu from '@/components/customer/NavMenu.vue'
import Logo from '@/components/customer/Logo.vue'
import Footer from '@/components/customer/Footer.vue'
import { useCartStore } from '@/stores/cart'

const backgroundColor = ref('bg-gradient-to-br from-[#FB4570] via-[#FB8DA0] to-[#FB4570]')

const cart = useCartStore()

// TypeScript interfaces
interface Product {
  id: number
  [key: string]: any
  name: string
  sku: string
  price: number
  compare_price?: number | null
  cost_price?: number | null
  stock_quantity: number
  min_stock_level?: number
  track_stock: boolean
  weight?: number | string
  dimensions?: string
  brand_id?: number | string
  short_description: string
  description: string
  meta_title?: string
  meta_description?: string
  is_active: boolean
  is_featured: boolean
  is_digital: boolean
  category_ids: Array<number | string>
  images: Array<{
    id: number
    image_path: string
    alt_text?: string
  }>
  attributes: Record<string, any>
}

interface Feature {
  id: number
  title: string
  description: string
  icon: string
}

// Props (would come from Laravel/Inertia)
const props = defineProps<{
  products: any
  featuredProducts: any
  cart?: any
  summary?: any
}>()

// Initialize cart with server data
onMounted(() => {
  if (props.cart && props.summary) {
    cart.initializeCart()
  }
  console.log('featuredProducts', props.featuredProducts)
})

// Reactive data
const email = ref('')
const subscriptionMessage = ref('')
const showSuccessMessage = ref(false)
const successMessage = ref('')
const loadingMessage = ref('')

// Track individual product loading states
const isAddingToCart = reactive<Record<number, boolean>>({})

// Sample data (would typically come from Laravel backend via Inertia props)
const features = ref<Feature[]>([
  {
    id: 1,
    title: 'Fast Delivery',
    description: 'Get your orders delivered within 24 hours with our express shipping',
    icon: '🚀'
  },
  {
    id: 2,
    title: 'Premium Quality',
    description: 'Every product is carefully curated and quality-tested',
    icon: '⭐'
  },
  {
    id: 3,
    title: '24/7 Support',
    description: 'Our customer service team is always here to help you',
    icon: '💬'
  }
])

// Methods
const formatPrice = (price: number): string => {
  return Number(price).toFixed(2)
}

const addToCart = async (product: Product) => {
  // Check if product is available
  if (!product.is_active || product.stock_quantity === 0) {
    showNotification('Product is not available', 'error')
    return
  }

  // Set individual loading state
  isAddingToCart[product.id] = true
  loadingMessage.value = `Adding ${product.name} to cart...`

  try {
    // Prepare CartItem object with quantity
    const cartItem = {
      id: product.id,
      name: product.name,
      price: product.price,
      quantity: 1
    }
    // Call the cart store method (this will handle Laravel communication)
    await cart.addItem(cartItem)

    // Show success message if no error occurred
    if (!cart.lastSyncError) {
      showNotification(`${product.name} added to cart!`, 'success')
    }
    
  } catch (error) {
    console.error('Failed to add product to cart:', error)
    showNotification('Failed to add product to cart', 'error')
  } finally {
    isAddingToCart[product.id] = false
    loadingMessage.value = ''
  }
}

const showNotification = (message: string, type: 'success' | 'error') => {
  if (type === 'success') {
    successMessage.value = message
    showSuccessMessage.value = true
    
    // Auto-hide success message after 3 seconds
    setTimeout(() => {
      showSuccessMessage.value = false
    }, 3000)
  }
  // Error messages are handled by the cart store's lastSyncError
}

const subscribeNewsletter = () => {
  if (email.value) {
    // This would typically make an API call to Laravel backend
    subscriptionMessage.value = 'Successfully subscribed! Welcome to ShopElite.'
    email.value = ''
    
    // Clear message after 3 seconds
    setTimeout(() => {
      subscriptionMessage.value = ''
    }, 3000)
  }
}

// Lifecycle
onMounted(() => {
  // Add scroll animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  }
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in')
      }
    })
  }, observerOptions)
  
  // Observe all sections
  document.querySelectorAll('section').forEach(section => {
    observer.observe(section)
  })
})

const getImageUrl = (path: any) => {
  return `/storage/${path}`
}

// Watch for cart loading state changes to provide better UX
watch(() => cart.isLoading, (isLoading) => {
  if (!isLoading) {
    // Clear individual loading states when global loading stops
    Object.keys(isAddingToCart).forEach(key => {
      isAddingToCart[Number(key)] = false
    })
    loadingMessage.value = ''
  }
})
</script>

<style>
/* Add some custom animations */
@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-up {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out forwards;
}

.animate-slide-up {
  animation: slide-up 0.8s ease-out forwards;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>