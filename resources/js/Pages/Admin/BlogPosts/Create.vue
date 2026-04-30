<template>
    <AdminLayout>

        <!-- Page header -->
        <div class="flex items-center gap-4 mb-6">
            <Link
                :href="route('admin.blog-posts.index')"
                class="text-sm text-gray-400 hover:text-gray-600 transition flex items-center gap-1"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Posts
            </Link>
            <span class="text-gray-300">/</span>
            <h1 class="text-xl font-semibold text-gray-900">New Post</h1>
        </div>

        <PostForm
            :form="form"
            submit-label="Publish Post"
            @submit="submit"
        />

    </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Admin/AdminLayout.vue';
import PostForm    from '@/Components/Admin/PostForm.vue';

const form = useForm({
    title:          '',
    description:    '',
    category:       '',
    author_name:    '',
    content:        '',
    publish_status: 0,
    cover_image:    null,
});

function submit() {
    form.post(route('admin.blog-posts.store'));
}
</script>
