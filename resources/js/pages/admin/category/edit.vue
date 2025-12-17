<script setup>
import { useForm } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import Form from '@/components/admin/ui/form/Form.vue'
import Label from '@/components/admin/ui/label/Label.vue'
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea'
import Checkbox from 'primevue/checkbox'
import Button from 'primevue/button'
import { useToast } from "primevue/usetoast";
import Toast from 'primevue/toast';



const toast = useToast();
const page = usePage()

const props = defineProps({
  category: Object,
  parentCategories: Array
})

const form = useForm({
  name: props.category.name,
  slug: props.category.slug ?? '',
  description: props.category.description ?? '',
  parent_id: props.category.parent_id,
  sort_order: props.category.sort_order ?? 0,
  is_active: props.category.is_active,
})

// Submit handler
const submit = () => {
  form.put(route('admin.category.update', props.category.id), {
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Success', detail: 'Category updated successfully!', life: 3000 });
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors);
    },
  });
}

</script>



<template>
  <div class="p-6">
   
    <Form @submit="submit">
      <!-- Name -->
      
        <Label class="block text-sm font-medium text-gray-700">Name</Label>
        <InputText
          v-model="form.name"
          type="text"
          class="input"
        />
        <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
      

      <!-- Slug -->
      
        <Label class="block text-sm font-medium text-gray-700">Slug</Label>
        <InputText
          v-model="form.slug"
          type="text"
          class="input"
        />
        <span v-if="form.errors.slug" class="text-red-500 text-sm">{{ form.errors.slug }}</span>
      

      <!-- Description -->
      
        <Label class="block text-sm font-medium text-gray-700">Description</Label>
        <Textarea
          v-model="form.description"
          class="input"
          rows="4"
        ></Textarea>
        <span v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</span>
      

      <!-- Parent Category -->
      
        <Label class="block text-sm font-medium text-gray-700">Parent Category</Label>
        <select v-model="form.parent_id" class="input">
          <option :value="null">None</option>
          <option
            v-for="cat in parentCategories"
            :key="cat.id"
            :value="cat.id"
          >
            {{ cat.name }}
          </option>
        </select>
        <span v-if="form.errors.parent_id" class="text-red-500 text-sm">{{ form.errors.parent_id }}</span>
     

      <!-- Sort Order -->
     
        <Label class="block text-sm font-medium text-gray-700">Sort Order</Label>
        <InputText
          v-model="form.sort_order"
          type="number"
          class="input"
        />
        <span v-if="form.errors.sort_order" class="text-red-500 text-sm">{{ form.errors.sort_order }}</span>
      

      <!-- Is Active -->
      
        <Label class="inline-flex items-center">
          <Checkbox
            type="checkbox"
            v-model="form.is_active"
            class="form-checkbox"
            binary="form.is_active"
          />
          <span class="ml-2 text-sm">Active</span>
        </Label>
        <span v-if="form.errors.is_active" class="text-red-500 text-sm">{{ form.errors.is_active }}</span>
     

      <!-- Submit -->
      
      <Toast />
        <Button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          :disabled="form.processing"
        >
          Update
        </Button>
      
    </Form>
  </div>
</template>

