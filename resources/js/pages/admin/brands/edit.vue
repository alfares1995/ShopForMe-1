<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import Form from '@/components/admin/ui/form/Form.vue'
import Label from '@/components/admin/ui/label/Label.vue'
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea'
import Checkbox from 'primevue/checkbox'
import Button from 'primevue/button'
import FileUpload from 'primevue/fileupload';
import { useToast } from "primevue/usetoast";
import Toast from 'primevue/toast';

const toast = useToast();
const page = usePage()
const editDialog = ref(false);

const props = defineProps<{
  brand: {
    id: number;
    name: string;
    slug?: string;
    description?: string;
    logo?: string;
    website?: string;
    is_active: boolean;
  },
}>()

const form = useForm({
  name: props.brand.name || '',
  slug: props.brand.slug || '',
  description: props.brand.description || '',
  logo: null as File | null,
  website: props.brand.website || '',
  is_active: props.brand.is_active ?? false,
  _method: 'PUT' as string, // Add _method to form definition
})

// Auto-generate slug from name if slug is empty
watch(() => form.name, (newName) => {
  if (!form.slug || form.slug === '') {
    form.slug = newName
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }
});

// Handle logo change
function handleLogoChange(event: any) {
  const files = event.files || event.target?.files;
  const file = files?.[0];
  
  if (file) {
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
      toast.add({ 
        severity: 'error', 
        summary: 'Error', 
        detail: 'File size must be less than 2MB', 
        life: 3000 
      });
      return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
      toast.add({ 
        severity: 'error', 
        summary: 'Error', 
        detail: 'Please select a valid image file (JPG, PNG, GIF, WEBP)', 
        life: 3000 
      });
      return;
    }
    
    form.logo = file;
  }
}

// Remove logo
function removeLogo() {
  form.logo = null;
}

// Submit handler
const submit = () => {
  // Clear previous errors
  form.clearErrors();

  // Add _method to form data for PUT request
  form._method = 'PUT';

  form.post(route('admin.brands.update', props.brand.id), {
    forceFormData: true, // Important for file uploads
    onSuccess: () => {
      toast.add({ 
        severity: 'success', 
        summary: 'Success', 
        detail: 'Brand updated successfully!', 
        life: 3000 
      });
      editDialog.value = false; // Close dialog on success
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors);
      toast.add({ 
        severity: 'error', 
        summary: 'Error', 
        detail: 'Please check the form for errors', 
        life: 3000 
      });
    },
  });
}

// Check if form has changes
const hasChanges = () => {
  return form.isDirty;
}
</script>

<template>
  <Form @submit="submit">
    <!-- Name Field -->
    
      <Label for="name" class="block text-sm font-medium text-gray-700">
        Name <span class="text-red-500">*</span>
      </Label>
      <InputText 
        id="name"
        v-model="form.name" 
        type="text" 
        
        :class="{ 'border-red-500': form.errors.name }"
        placeholder="Enter brand name"
      />
      <span v-if="form.errors.name" class="text-red-500 text-sm block mt-1">
        {{ form.errors.name }}
      </span>
    

    <!-- Slug Field -->
    
      <Label for="slug" class="block text-sm font-medium text-gray-700">Slug</Label>
      <InputText 
        id="slug"
        v-model="form.slug" 
        type="text" 
        :class="{ 'border-red-500': form.errors.slug }"
        placeholder="Auto-generated from name"
      />
      <small class="text-gray-500">Leave empty to auto-generate from name</small>
      <span v-if="form.errors.slug" class="text-red-500 text-sm block mt-1">
        {{ form.errors.slug }}
      </span>
    

    <!-- Description Field -->
   
      <Label for="description" class="block text-sm font-medium text-gray-700">Description</Label>
      <Textarea 
        id="description"
        v-model="form.description" 
        rows="3" 
        :class="{ 'border-red-500': form.errors.description }"
        placeholder="Enter brand description"
      />
      <span v-if="form.errors.description" class="text-red-500 text-sm block mt-1">
        {{ form.errors.description }}
      </span>
    

    <!-- Logo Field -->
    
      <Label class="block text-sm font-medium text-gray-700">Logo</Label>
      
      <!-- Current Logo Preview -->
      <div v-if="props.brand.logo && !form.logo" class="mb-3">
        <img 
          :src="`/storage/${props.brand.logo}`" 
          :alt="props.brand.name"
          class="h-16 w-16 object-cover rounded border"
        >
        <p class="text-sm text-gray-500 mt-1">Current logo</p>
      </div>

      <!-- New Logo Preview -->
<!-- New Logo Preview -->
      <div v-if="form.logo && typeof URL !== 'undefined'" class="mb-3">
        <img 
          :src="URL.createObjectURL(form.logo)" 
          :alt="'New logo preview'"
          class="h-16 w-16 object-cover rounded border"
        >
        <div class="flex items-center gap-2 mt-2">
          <span class="text-sm text-green-600">New logo selected</span>
          <Button 
            type="button" 
            size="small" 
            severity="secondary" 
            outlined
            @click="removeLogo"
          >
            Remove
          </Button>
        </div>
      </div>

      <FileUpload 
        mode="basic" 
        name="logo" 
        accept="image/*" 
        :maxFileSize="2048000"
        @select="handleLogoChange"
        :auto="false"
        chooseLabel="Choose Logo"
        class="w-full"
      />
      <small class="text-gray-500">Max file size: 2MB. Formats: JPG, PNG, GIF, WEBP</small>
      <span v-if="form.errors.logo" class="text-red-500 text-sm block mt-1">
        {{ form.errors.logo }}
      </span>
    

    <!-- Website Field -->
    
      <Label for="website" class="block text-sm font-medium text-gray-700">Website</Label>
      <InputText 
        id="website"
        v-model="form.website" 
        type="url" 
        class="input w-full"
        :class="{ 'border-red-500': form.errors.website }"
        placeholder="https://example.com"
      />
      <span v-if="form.errors.website" class="text-red-500 text-sm block mt-1">
        {{ form.errors.website }}
      </span>
    

    <!-- Active Status -->
    <div class="field mt-4">
      <div class="flex items-center">
        <Checkbox 
          id="is_active"
          v-model="form.is_active" 
          binary
          :class="{ 'border-red-500': form.errors.is_active }"
        />
        <Label for="is_active" class="ml-2 cursor-pointer">Active</Label>
      </div>
      <small class="text-gray-500">Check to make this brand visible to customers</small>
      <span v-if="form.errors.is_active" class="text-red-500 text-sm block mt-1">
        {{ form.errors.is_active }}
      </span>
    </div>
    
    <!-- Submit Button -->
    
      <Button 
        type="submit" 
        label="Update Brand" 
        :loading="form.processing"
        :disabled="form.processing"
        class="flex-1"
        @submit="editDialog = false"
      />
    
    
  </Form>

  <Toast />
</template>

