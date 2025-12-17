<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Form from '@/components/admin/ui/form/Form.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import InputSwitch from 'primevue/inputswitch' 
import SelectButton from 'primevue/selectbutton'
import MultiSelect from 'primevue/multiselect'
import Textarea from 'primevue/textarea'
import Checkbox from 'primevue/checkbox'
import Button from 'primevue/button'
import FileUpload from 'primevue/fileupload'
import { Option } from 'lucide-vue-next'

const page = usePage()

const categories = computed<Array<{ id: number | string; name: string }>>(() => page.props.categories as Array<{ id: number | string; name: string }>)
const brands = computed<Array<{ id: number | string; name: string }>>(() => page.props.brands as Array<{ id: number | string; name: string }>)
const attributes = computed<Array<any>>(() => page.props.attributes as Array<any>)

interface FormData {
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
  images: Array<{ image_path: File | string; alt_text: string; is_primary: boolean }>
  attributes: Record<string, any>
}


const form = useForm<FormData>({
  name: '',
  sku: '',
  price: 0,
  compare_price: null,
  cost_price: null,
  stock_quantity: 0,
  min_stock_level: 0,
  track_stock: false,
  weight: '',
  dimensions: '',
  brand_id: '',
  short_description: '',
  description: '',
  meta_title: '',
  meta_description: '',
  is_active: true,
  is_featured: false,
  is_digital: false,
  category_ids: [],
  images: [],
  attributes: {}
})

const errors = computed(() => page.props.errors || {})

function submit() {
  form.post(route('admin.product.store'), {
    onSuccess: () => {
      form.reset()
      form.images = []
      console.log('Form submitted successfully')
    },
    onError: (errors) => {
      console.error('Form submission failed:', errors)
    }
  })
}


function addImage() {
  form.images.push({
    image_path: '',
    alt_text: '',
    is_primary: false
  })
}

function removeImage(index: number) {
  form.images.splice(index, 1)
}
function handleImageChange(index: number) {
  const input = document.querySelectorAll('input[type="file"]')[index] as HTMLInputElement
  if (input.files && input.files.length > 0) {
    form.images[index].image_path = input.files[0]
  }
}

function setPrimaryImage(index: number) {
  form.images.forEach((img, i) => {
    img.is_primary = i === index
  })
}
</script>


<template>
<AdminLayout>
    <h1 class="text-2xl font-bold">Create Product</h1>

    <Form @submit="submit" >

        <div class="flex flex-row space-x-7">
        <!-- Name -->

          <div class="basis-1/2">
            <label class="block font-medium">Product Name</label>
            <InputText class="w-full" v-model="form.name" placeholder="Enter product name" />
          <div v-if="errors.name" class="text-red-500">{{ errors.name }}</div>
        </div>

        <!-- SKU -->

        <div class="basis-1/2">
          <label class="block font-medium">SKU</label>
          <InputText class="w-full" v-model="form.sku" placeholder="Enter product SKU" type="text"/>
          <div v-if="errors.sku" class="text-red-500">{{ errors.sku }}</div>
        </div>
        </div>
        <div class="flex flex-row space-x-7">
        <!-- Price -->
        <div class="basis-1/2">
          <label class="block font-medium">Price</label>

          <InputNumber v-model="form.price" inputId="stacked-buttons" placeholder="Enter product price" mode="currency" currency="GBP" fluid />
          <div v-if="errors.price" class="text-red-500">{{ errors.price }}</div>
        </div>

        <!-- Compare Price -->

        <div class="basis-1/2">
          <label class="block font-medium">Compare Price</label>
          <InputNumber v-model.number="form.compare_price" placeholder="Enter compare price" inputId="stacked-buttons" mode="currency" currency="GBP" fluid />
        </div>
        </div>
        <div class="flex flex-row space-x-7">
        <!-- Brand -->
        <select v-model="form.brand_id" class="w-full">
          <option value="">Select Brand</option>
          <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
        </select>
        <select v-model="form.category_ids" multiple class="w-full">
          <option value="">Select Categories</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
        </select>
      </div>

      <!-- Descriptions -->
       
        <label class="block font-medium">Description</label>
        <Textarea v-model="form.description" class="input"></Textarea>
      
        <label class="block font-medium">Short Description</label>
        <Textarea v-model="form.short_description" class="input"></Textarea>

      

      <!-- Stock Details -->
      
        <div class="flex flex-row space-x-7">
          <div class="basis-1/2">
          <label class="block font-medium">Stock Quantity</label>
          <InputNumber v-model="form.stock_quantity" type="number" class="input" />
          </div>

          <div class="basis-1/2">
          <label class="block font-medium">Min Stock Level</label>
          <InputNumber v-model="form.min_stock_level" type="number" class="input" />
          </div>
          <div class="basis-1/2">
          <label class="block font-medium">Track Stock</label>
          <Checkbox v-model="form.track_stock" binary />
          </div>
          </div>
      <!-- Images -->
      
        <label class="block font-medium">Images</label>
        <div v-for="(image, index) in form.images" :key="index">
          <input @change="handleImageChange(index)" type="file" placeholder="Image URL"  />
          <InputText v-model="image.alt_text" placeholder="Alt Text" />
          <label class="flex items-center space-x-1">
            <input type="radio" :checked="image.is_primary" @change="setPrimaryImage(index)" />
            <span>Primary</span>
          </label>
          <Button type="button" @click="removeImage(index)" class="text-red-600">Remove</button>
        </div>
        <Button type="button" @click="addImage" class="w-3xs">Add Image</Button>

      <!-- Attributes -->
      <div v-if="attributes.length">
        <h2 class="text-lg font-semibold">Attributes</h2>
        <div v-for="attribute in attributes" :key="attribute.id" class="mb-3">
          <label class="block">{{ attribute.name }}</label>
          <input v-model="form.attributes[attribute.id]" type="text" class="input" />
        </div>
      </div>

      <!-- Booleans -->
      <div class="flex gap-4 flex-wrap">
        <label class="flex items-center space-x-1">
          <Checkbox type="checkbox" v-model="form.is_active" binary/>
          <span>Active</span>
        </label>
        <label class="flex items-center space-x-1">
          <Checkbox type="checkbox" v-model="form.is_featured" binary/>
          <span>Featured</span>
        </label>
        <label class="flex items-center space-x-1">
          <Checkbox type="checkbox" v-model="form.is_digital" binary/>
          <span>Digital</span>
        </label>
      </div>

      
        <Button type="submit" class="btn-primary">Create Product</button>
      
    </Form>
</AdminLayout>
</template>



