<script lang="ts" setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import Logo from './Logo.vue'
import { BookOpen, Folder, LayoutGrid, ShoppingCart } from 'lucide-vue-next';
import { useCartStore } from '@/stores/cart'
import MiniCart from '../customer/MiniCart.vue';


import 'primeicons/primeicons.css'



const showCartDropdown = ref(false)
const toggleCartDropdown = () => {
  showCartDropdown.value = !showCartDropdown.value
}




// refs for dynamic styling
const textColor = ref('text-black-300')
const hoverColor = ref('hover:text-white')

const cart = useCartStore()

cart.getItems()

// Define a type for a menu item
interface NavItem {
  id: number
  name: string | null
  href: string
  icon: any | null // Accepts a component or null
}



// Create reactive nav items
const navItems = ref<NavItem[]>([
  {
    name: 'Home', href: '/', icon: null,
    id: 0
  },
  {
    name: 'Products', href: '/products', icon: null,
    id: 1
  },
  {
    name: 'Categories', href: '/categories', icon: null,
    id: 2
  },
  {
    name: 'Contact', href: '/chat', icon: null,
    id: 3
  },
  {
    name: 'About Us', href: '/about', icon: null,
    id: 4
  },
  {
    name: 'Blog', href: '/blog', icon: null,
    id: 5
  },
  {
    name: 'FAQ', href: '/faq', icon: null,
    id: 6
  },
 
  
])

// Reactive state for mobile menu
// This state will control the visibility of the mobile menu
const mobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}
const handleMouseOver = (id: number) => {
  // Handle mouse over event for the icon
  
}
function removeFromCart(productId: number) {
  router.delete(`/cart/remove/${productId}`)
}
</script>
<template>
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/20 backdrop-blur-lg border-b border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
         <logo/>
          <div  class="hidden md:block">
            <div class="flex items-center space-x-8">
                <div v-for="item in navItems" :key="item.id">
                    <Link :href="item.href" :class="[textColor, hoverColor, 'transition-colors duration-200', 'flex', 'items-center', 'gap-2']">
                      <component @click="toggleCartDropdown" v-if="item.icon" :is="item.icon" class="w-8 h-8 text-black" />
                      {{ item.name }}
                    </Link>
                    
                </div>
                <div class="relative">
  <button @click="toggleCartDropdown" class="relative flex items-center gap-2">
    <ShoppingCart class="w-8 h-8" />
    <span
      v-if="cart.itemCount > 0"
      class="absolute -top-2 -right-2 bg-red-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center"
    >
      {{ cart.itemCount }}
    </span>
  </button>

  <!-- Cart Dropdown -->
  <div v-if="showCartDropdown" class="absolute right-0 mt-2 w-64 bg-white text-black rounded-lg shadow-xl z-50 p-4">
    <h3 class="font-bold mb-2">Your Cart</h3>
    <div v-if="cart.itemCount > 0">
      <div v-for="item in cart.items" :key="item.id" class="mb-2">
        <div class="flex justify-between items-center">
          <span>{{ item.name }}</span>
          <span>£{{ item.price * item.quantity }}</span>
          <input type="text" v-model.number="item.quantity" class="w-12 text-center border rounded" @change="cart.updateQuantity(item.id, item.quantity)" />
          <span>x{{ item.quantity }}</span>
          <button @click="cart.removeItem(item.id)" class="text-red-600 hover:text-red-800">
            Remove
          </button>
        </div>
      </div>
      <h1 class="font-bold mt-4">Total: £{{ cart.totalPrice }}</h1>  
    </div>
    <div v-else class="text-sm text-gray-500">Your cart is empty.</div>
    <div class="mt-4 text-center">
      <Link href="/cart" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-full transition-all duration-200 transform hover:scale-105">
        View Cart
      </Link>
      <Link href="/checkout" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-full transition-all duration-200 transform hover:scale-105">
        Checkout
      </Link>
    </div>
  </div>
</div>


                <Link :href="route('login')" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-full transition-all duration-200 transform hover:scale-105">
                  Sign In
                </Link>
            </div>
           
          </div>
           
          <div class="md:hidden">
            <button @click="toggleMobileMenu" :class="[textColor, hoverColor]">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Mobile menu -->
      <div v-if="mobileMenuOpen" class="md:hidden bg-black/30 backdrop-blur-lg">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <div v-for="item in navItems" :key="item.id">
                <Link :href="item.href" :class="[textColor, hoverColor, 'block px-3 py-2', 'flex', 'items-center', 'gap-2']">
                  <component v-if="item.icon" :is="item.icon" class="w-4 h-4" />
                  {{ item.name }}
                </Link>
            </div>
        </div>
        <Link :href="route('login')">
        <button  class="w-full text-left px-3 py-2 bg-purple-600 text-white rounded-lg mt-2">
            Sign In
        </button></Link>
      </div>
    </nav>



</template>