<script lang="ts" setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Form from '@/components/admin/ui/form/Form.vue';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Label from '@/components/ui/label/Label.vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';


interface BrandForm {
  name: string;
  description: string;
  logo: File | null;
  website: string;
  is_active: boolean;
}

const form = useForm({
  name: '',
  description: '',
  logo: null,
  website: '',
  is_active: true,
})


const toast = useToast();


function handleLogoChange(e : Event) {
    const target = e.target as HTMLInputElement
    if (target.files && target.files.length > 0) {
        form.logo = target.files[0]
    }
}

function submit() {
  form.post(route('admin.brands.store'), {
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Success', detail: 'Brand created successfully!', life: 3000 });
      
    }
  , onError: (errors) => {
      console.error('Form submission failed:', errors)
    }
  })
}


</script>



<template>

<div class="flex justify-center flex-col items-center">
    <h1 class="text-2xl font-bold mb-4">Create Brand</h1>
</div>

<Form  @submit="submit" class="">

    <Label class=" font-bold text-gray-700">Brand Name</Label>
    <InputText v-model="form.name" type="text" />

    <Label class=" font-bold text-gray-700">Description</Label>
    <InputText v-model="form.description" type="text"/>
    <Label class=" font-bold text-gray-700">Logo</Label>
    <InputText @change="handleLogoChange" type="file"/>
    <Label class="font-bold text-gray-700">Website</Label>
    <InputText v-model="form.website" type="text"/>
    
    <Checkbox v-model="form.is_active" class="mb-4" binary/>
    <Toast />
    <Button type="submit"  :disabled="form.processing" des>
        Create Brand
    </Button>
</Form>

    
</template>