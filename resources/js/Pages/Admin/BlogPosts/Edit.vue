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
            <h1 class="text-xl font-semibold text-gray-900 truncate max-w-xs">{{ post.title }}</h1>
        </div>

        <PostForm
            :form="form"
            :existing-image-url="post.cover_image_url"
            :categories="categories"
            :authors="authors"
            submit-label="Update Post"
            @submit="submit"
        />

    </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Admin/AdminLayout.vue';
import PostForm    from '@/Components/Admin/PostForm.vue';

const props = defineProps({
    post:       { type: Object, required: true },
    categories: { type: Array,  default: () => [] },
    authors:    { type: Array,  default: () => [] },
});

const form = useForm({
    title:          props.post.title,
    slug:           props.post.slug,
    description:    props.post.description,
    category_id:    props.post.category_id,
    author_id:      props.post.author_id,
    content:        props.post.content,
    publish_status: props.post.publish_status ? 1 : 0,
    cover_image:    null,
    tags:           props.post.tags ?? [],
});

function submit() {
    form.put(route('admin.blog-posts.update', props.post.slug));
}
</script>
