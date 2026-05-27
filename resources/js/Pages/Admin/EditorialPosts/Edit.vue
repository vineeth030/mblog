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
            <h1 class="text-xl font-semibold text-gray-900 truncate max-w-xs">{{ post.title }}</h1>
        </div>

        <EditorialForm
            :form="form"
            :existing-image-url="post.featured_image_url"
            submit-label="Update Editorial"
            @submit="submit"
        />

    </AdminLayout>
</template>

<script setup>
import { Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout    from '@/Components/Admin/AdminLayout.vue';
import EditorialForm  from '@/Components/Admin/EditorialForm.vue';

const props = defineProps({
    post: { type: Object, required: true },
});

const form = useForm({
    _method:          'put',
    title:            props.post.title,
    slug:             props.post.slug,
    excerpt:          props.post.excerpt ?? '',
    content:          props.post.content ?? '',
    featured_image:   null,
    status:           props.post.status,
    is_featured:      !!props.post.is_featured,
    published_at:     props.post.published_at ?? '',
    meta_title:       props.post.meta_title ?? '',
    meta_description: props.post.meta_description ?? '',
});

function submit() {
    // Use POST + _method spoofing so multipart file uploads work on update
    form.post(route('admin.editorial-posts.update', props.post.slug));
}
</script>
