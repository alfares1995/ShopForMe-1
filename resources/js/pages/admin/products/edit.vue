<script setup>
import { ref, reactive, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { 
  ArrowLeftIcon, 
  EyeIcon, 
  CloudArrowUpIcon, 
  XMarkIcon 
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  product: Object,
  categories: Array,
  brands: Array,
  attributes: Array,
  errors: Object
})

// Form data
const form = useForm({
  name: props.product.name || '',
  description: props.product.description || '',
  short_description: props.product.short_description || '',
  sku: props.product.sku || '',
  price: props.product.price || '',
  compare_price: props.product.compare_price || '',
  cost_price: props.product.cost_price || '',
  stock_quantity: props.product.stock_quantity || 0,
  min_stock_level: props.product.min_stock_level || 0,
  track_stock: props.product.track_stock || false,
  weight: props.product.weight || '',
  dimensions: props.product.dimensions || '',
  brand_id: props.product.brand_id || '',
  meta_title: props.product.meta_title || '',
  meta_description: props.product.meta_description || '',
  is_active: props.product.is_active || false,
  is_featured: props.product.is_featured || false,
  is_digital: props.product.is_digital || false,
  category_ids: props.product.categories?.map(cat => cat.id) || [],
  attributes: props.product.attribute_values?.reduce((acc, attr) => {
    acc[attr.product_attribute_id] = attr.value
    return acc
  }, {}) || {}
})

// New images handling
const newImages = ref([])

// Methods
const handleImageUpload = (event) => {
  const files = Array.from(event.target.files)
  
  files.forEach((file, index) => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader()
      reader.onload = (e) => {
        newImages.value.push({
          file: file,
          preview: e.target.result,
          alt_text: '',
          is_primary: newImages.value.length === 0 && props.product.images?.length === 0
        })
      }
      reader.readAsDataURL(file)
    }
  })
  
  // Reset the input
  event.target.value = ''
}

const removeNewImage = (index) => {
  newImages.value.splice(index, 1)
}

const handlePrimaryImageChange = (index) => {
  // Only one primary image allowed
  newImages.value.forEach((img, i) => {
    if (i !== index) {
      img.is_primary = false
    }
  })
}

const deleteImage = (imageId) => {
  if (confirm('Are you sure you want to delete this image?')) {
    router.delete(route('admin.product.image.destroy', { product: props.product.id, image: imageId }), {
      preserveScroll: true,
      onSuccess: () => {
        // The page will be refreshed automatically
      }
    })
  }
}

const updateProduct = () => {
  form.transform((data) => {
    return {
      ...data,
      _method: 'put',
      images: newImages.value.map(img => ({
        image_path: img.file,
        alt_text: img.alt_text,
        is_primary: img.is_primary
      }))
    }
  }).post(route('admin.product.update', props.product.id), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      newImages.value = []
    }
  })
}

const deleteProduct = () => {
  if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
    router.delete(route('admin.product.destroy', props.product.id))
  }
}

// Processing state
const processing = computed(() => form.processing)
</script>


<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center space-x-4">
            <Link 
              :href="route('admin.product.index')" 
              class="text-gray-400 hover:text-gray-600 transition-colors"
            >
              <ArrowLeftIcon class="h-6 w-6" />
            </Link>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
              <p class="text-sm text-gray-500">Update product information and settings</p>
            </div>
          </div>
          <div class="flex space-x-3">
            <Link 
              :href="route('admin.product.show', product.id)"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
            >
              <EyeIcon class="h-4 w-4 mr-2" />
              View Product
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="updateProduct" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Product Name *
                  </label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.name }"
                    placeholder="Enter product name"
                  />
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    SKU *
                  </label>
                  <input
                    v-model="form.sku"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.sku }"
                    placeholder="Enter SKU"
                  />
                  <p v-if="errors.sku" class="mt-1 text-sm text-red-600">{{ errors.sku }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Brand
                  </label>
                  <select
                    v-model="form.brand_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Select Brand</option>
                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                      {{ brand.name }}
                    </option>
                  </select>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Short Description
                  </label>
                  <textarea
                    v-model="form.short_description"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.short_description }"
                    placeholder="Brief product description..."
                  ></textarea>
                  <p v-if="errors.short_description" class="mt-1 text-sm text-red-600">{{ errors.short_description }}</p>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Description *
                  </label>
                  <textarea
                    v-model="form.description"
                    rows="6"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.description }"
                    placeholder="Detailed product description..."
                  ></textarea>
                  <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                </div>
              </div>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Pricing</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Price *
                  </label>
                  <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">£</span>
                    <input
                      v-model="form.price"
                      type="number"
                      step="0.01"
                      min="0"
                      class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      :class="{ 'border-red-500': errors.price }"
                      placeholder="0.00"
                    />
                  </div>
                  <p v-if="errors.price" class="mt-1 text-sm text-red-600">{{ errors.price }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Compare Price
                  </label>
                  <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">£</span>
                    <input
                      v-model="form.compare_price"
                      type="number"
                      step="0.01"
                      min="0"
                      class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      :class="{ 'border-red-500': errors.compare_price }"
                      placeholder="0.00"
                    />
                  </div>
                  <p v-if="errors.compare_price" class="mt-1 text-sm text-red-600">{{ errors.compare_price }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Cost Price
                  </label>
                  <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">£</span>
                    <input
                      v-model="form.cost_price"
                      type="number"
                      step="0.01"
                      min="0"
                      class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="0.00"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Inventory -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Inventory</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Stock Quantity *
                  </label>
                  <input
                    v-model="form.stock_quantity"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.stock_quantity }"
                    placeholder="0"
                  />
                  <p v-if="errors.stock_quantity" class="mt-1 text-sm text-red-600">{{ errors.stock_quantity }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Minimum Stock Level
                  </label>
                  <input
                    v-model="form.min_stock_level"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0"
                  />
                </div>

                <div class="md:col-span-2">
                  <label class="flex items-center">
                    <input
                      v-model="form.track_stock"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    />
                    <span class="ml-2 text-sm text-gray-700">Track stock quantity</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Product Images -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Product Images</h2>
              
              <!-- Existing Images -->
              <div v-if="product.images && product.images.length > 0" class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Current Images</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div 
                    v-for="image in product.images" 
                    :key="image.id"
                    class="relative group"
                  >
                    <img 
                      :src="'/storage/' + image.image_path" 
                      :alt="image.alt_text"
                      class="w-full h-32 object-cover rounded-lg border"
                    />
                    <div class="absolute top-2 right-2">
                      <span 
                        v-if="image.is_primary" 
                        class="bg-blue-500 text-white text-xs px-2 py-1 rounded"
                      >
                        Primary
                      </span>
                    </div>
                    <button
                      type="button"
                      @click="deleteImage(image.id)"
                      class="absolute top-2 left-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      <XMarkIcon class="h-4 w-4" />
                    </button>
                  </div>
                </div>
              </div>

              <!-- Add New Images -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                  Add New Images
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                  <CloudArrowUpIcon class="mx-auto h-12 w-12 text-gray-400" />
                  <div class="mt-4">
                    <label class="cursor-pointer">
                      <span class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                        Choose Images
                      </span>
                      <input
                        type="file"
                        multiple
                        accept="image/*"
                        class="hidden"
                        @change="handleImageUpload"
                      />
                    </label>
                  </div>
                  <p class="mt-2 text-sm text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                </div>
              </div>

              <!-- New Images Preview -->
              <div v-if="newImages.length > 0" class="mt-6">
                <h3 class="text-sm font-medium text-gray-700 mb-3">New Images to Upload</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div 
                    v-for="(image, index) in newImages" 
                    :key="index"
                    class="relative group"
                  >
                    <img 
                      :src="image.preview" 
                      :alt="image.alt_text"
                      class="w-full h-32 object-cover rounded-lg border"
                    />
                    <button
                      type="button"
                      @click="removeNewImage(index)"
                      class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      <XMarkIcon class="h-4 w-4" />
                    </button>
                    <div class="mt-2">
                      <input
                        v-model="image.alt_text"
                        type="text"
                        placeholder="Alt text"
                        class="w-full text-xs px-2 py-1 border border-gray-300 rounded"
                      />
                      <label class="flex items-center mt-1">
                        <input
                          v-model="image.is_primary"
                          type="checkbox"
                          class="text-xs"
                          @change="handlePrimaryImageChange(index)"
                        />
                        <span class="ml-1 text-xs text-gray-700">Primary</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Attributes -->
            <div v-if="attributes && attributes.length > 0" class="bg-white rounded-lg shadow-sm border p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Product Attributes</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="attribute in attributes" :key="attribute.id">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ attribute.name }}
                  </label>
                  <input
                    v-model="form.attributes[attribute.id]"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :placeholder="'Enter ' + attribute.name.toLowerCase()"
                  />
                </div>
              </div>
            </div>

            <!-- SEO -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">SEO Settings</h2>
              
              <div class="space-y-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Meta Title
                  </label>
                  <input
                    v-model="form.meta_title"
                    type="text"
                    maxlength="60"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.meta_title }"
                    placeholder="SEO title for search engines"
                  />
                  <p class="mt-1 text-xs text-gray-500">{{ form.meta_title?.length || 0 }}/60 characters</p>
                  <p v-if="errors.meta_title" class="mt-1 text-sm text-red-600">{{ errors.meta_title }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Meta Description
                  </label>
                  <textarea
                    v-model="form.meta_description"
                    rows="3"
                    maxlength="160"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.meta_description }"
                    placeholder="Brief description for search engine results"
                  ></textarea>
                  <p class="mt-1 text-xs text-gray-500">{{ form.meta_description?.length || 0 }}/160 characters</p>
                  <p v-if="errors.meta_description" class="mt-1 text-sm text-red-600">{{ errors.meta_description }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-8">
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Actions</h2>
              
              <div class="space-y-4">
                <button
                  type="submit"
                  :disabled="processing"
                  class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  <span v-if="processing">Updating...</span>
                  <span v-else>Update Product</span>
                </button>

                <button
                  type="button"
                  @click="deleteProduct"
                  class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors"
                >
                  Delete Product
                </button>
              </div>
            </div>

            <!-- Status -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Status</h2>
              
              <div class="space-y-4">
                <label class="flex items-center">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  />
                  <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>

                <label class="flex items-center">
                  <input
                    v-model="form.is_featured"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  />
                  <span class="ml-2 text-sm text-gray-700">Featured</span>
                </label>

                <label class="flex items-center">
                  <input
                    v-model="form.is_digital"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  />
                  <span class="ml-2 text-sm text-gray-700">Digital Product</span>
                </label>
              </div>
            </div>

            <!-- Categories -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Categories</h2>
              
              <div class="space-y-3 max-h-64 overflow-y-auto">
                <label 
                  v-for="category in categories" 
                  :key="category.id"
                  class="flex items-center"
                >
                  <input
                    v-model="form.category_ids"
                    :value="category.id"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  />
                  <span class="ml-2 text-sm text-gray-700">{{ category.name }}</span>
                </label>
              </div>
            </div>

            <!-- Product Details -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Product Details</h2>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Weight (kg)
                  </label>
                  <input
                    v-model="form.weight"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0.00"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Dimensions
                  </label>
                  <input
                    v-model="form.dimensions"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="L x W x H"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

