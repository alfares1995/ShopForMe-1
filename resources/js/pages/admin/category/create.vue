<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3';
import Form from '@/components/admin/ui/form/Form.vue';
import Label from '@/components/admin/ui/label/Label.vue';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import { useToast } from "primevue/usetoast";
import Toast from 'primevue/toast';


const toast = useToast();

interface CategoryForm {
    name: string | null;
    slug: string | null;
    description: string | null;
    image: File | null;
    is_active: boolean;
    meta_title: string | null;
    meta_description: string | null;
    sort_order: any;
}

defineProps<{
    category?: CategoryForm;
}>();

const form = useForm({
    name: null,
    slug: null,
    description: null,
    image: null,
    is_active: true,
    meta_title: null,
    meta_description: null,
    sort_order: 0,
});

const submit = () => {
    form.post('/admin/category', {
        onSuccess: () => {
            form.reset();
            toast.add({ severity: 'success', summary: 'Success', detail: 'Category created successfully!', life: 3000 });
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors);
        },
    });
};

</script>

<template>
    <Form @submit="submit">
        <Label class="font-bold text-gray-700">Category Name</Label>
        <InputText v-model="form.name" type="text" required />

        <Label class="font-bold text-gray-700">Description</Label>
        <Textarea v-model="form.description" />

        <Label class="font-bold text-gray-700">Sort Order</Label>
        <InputText v-model="form.sort_order" type="number" class="input" />

        <Checkbox v-model="form.is_active" label="Active" binary />
        <Toast />
        <Button type="submit">
            Create Category
        </Button>
    </Form>
</template>