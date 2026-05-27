<template>
    <AdminLayout>

        <!-- Page header -->
        <div class="flex items-center gap-4 mb-6">
            <Link
                :href="route('admin.editorial-posts.index')"
                class="text-sm text-gray-400 hover:text-gray-600 transition flex items-center gap-1"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Editorial
            </Link>
            <span class="text-gray-300">/</span>
            <h1 class="text-xl font-semibold text-gray-900">New Editorial</h1>
        </div>

        <EditorialForm
            :form="form"
            submit-label="Publish Editorial"
            @submit="submit"
        />

    </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout    from '@/Components/Admin/AdminLayout.vue';
import EditorialForm  from '@/Components/Admin/EditorialForm.vue';

const form = useForm({
    title:            '',
    slug:             '',
    excerpt:          '',
    content:          '',
    featured_image:   null,
    status:           'draft',
    is_featured:      false,
    published_at:     '',
    meta_title:       '',
    meta_description: '',
});

function submit() {
    form.post(route('admin.editorial-posts.store'));
}
</script>
