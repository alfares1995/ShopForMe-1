<script lang="ts" setup>
import Logo from './Logo.vue'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
// Reactive state for footer text color
const footerTextColor = ref('text-gray-300')
// Reactive state for footer background color
const footerBgColor = ref('bg-pink-900 dark:bg-gray-800')
// Reactive state for footer border color
const footerBorderColor = ref('border-white/10')
// Reactive state for footer hover color
const footerHoverColor = ref('hover:text-white')
// Reactive state for footer link color
const footerLinkColor = ref('text-gray-300 hover:text-purple-400 transition-colors duration-200')
// Reactive state for footer logo color
const footerLogoColor = ref('text-black')
// Reactive state for footer logo highlight color
const footerLogoHighlightColor = ref('text-blue-600 dark:text-sky-400')
// Reactive state for footer logo size
const footerLogoSize = ref('text-2xl')
// Reactive state for footer logo font weight
const footerLogoFontWeight = ref('font-bold')
// Reactive state for footer logo font family
const footerLogoFontFamily = ref('font-sans')
// Reactive state for footer logo text
const footerLogoText = ref('ShopForme')

interface FooterLink {
  name: string
  href: string
}
// Reactive state for footer links
const footerLinks = ref<FooterLink[]>([
  { name: 'Home', href: '#' },
  { name: 'Products', href: '#' },
  { name: 'Categories', href: '#' },
  { name: 'About Us', href: '#' },
  { name: 'Contact Us', href: '#' }
])

// Reactive state for footer service links
const footerServiceLinks = ref<FooterLink[]>([
  { name: 'Customer Service', href: '#' },
  { name: 'Returns', href: '#' },
  { name: 'Shipping Info', href: '#' },
  { name: 'FAQ', href: '#' }
])
// Reactive state for social media links
const socialMediaLinks = ref([
  { name: 'Facebook', href: 'https://www.facebook.com', icon: '📘' },
  { name: 'Twitter', href: 'https://www.twitter.com', icon: '🐦' },
  { name: 'Instagram', href: 'https://www.instagram.com', icon: '📷' },
  { name: 'LinkedIn', href: 'https://www.linkedin.com', icon: '🔗' }
])

</script>

<template>
     <!-- Footer -->
    <footer :class="[footerBgColor, 'backdrop-blur-lg py-12 px-4 sm:px-6 lg:px-8 border-t border-white/10']">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <Logo/>
            <p :class="[footerTextColor]">Premium shopping experience with unmatched quality and service.</p>
          </div>
          
          <div>
            <h3 :class="[footerTextColor, 'font-semibold mb-4']">Quick Links</h3>
            <ul v-for="link in footerLinks" :key="link.name" class="space-y-2">
              <li><Link :href="link.href" :class="[footerLinkColor]">{{ link.name }}</Link></li>
            </ul>
          </div>
          
          <div>
            <h3 :class="[footerTextColor, 'font-semibold mb-4']">Customer Service</h3>
            <ul v-for="value in footerServiceLinks" :key="value.name" class="space-y-2">
              <li><Link :href="value.href" :class="[footerLinkColor]">{{ value.name }}</Link></li>
            </ul>
          </div>
          
          <div>
            <h3 :class="[footerTextColor, 'font-semibold mb-4']">Connect</h3>
            <div class="flex space-x-4">
                <div v-for="social in socialMediaLinks" :key="social.name">
                    <a :href="social.href" :class="[footerLinkColor, 'transition-colors text-xl']">{{ social.icon }}</a>
                </div>
              
            </div>
          </div>
        </div>

        <div :class="[footerBorderColor, 'mt-8 pt-8 text-center']">
          <p :class="[footerTextColor]">&copy;{{ new Date().getUTCFullYear() }} ShopForme. All rights reserved.</p>
        </div>
      </div>
    </footer>
</template>