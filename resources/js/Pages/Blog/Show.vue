<template>
    <PublicLayout>

        <!-- Hero image -->
        <div
            v-if="post.cover_image_url"
            class="w-full bg-gray-100 max-h-[480px] overflow-hidden"
        >
            <img
                :src="post.cover_image_url"
                :alt="post.title"
                class="w-full max-h-[480px] object-cover"
            />
        </div>

        <div class="max-w-2xl mx-auto px-6">

            <!-- Back link -->
            <div class="pt-8">
                <Link
                    :href="route('blog.index')"
                    class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-700 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    All posts
                </Link>
            </div>

            <!-- Post header -->
            <header class="mt-6">
                <Link
                    :href="route('blog.index', { category: post.category })"
                    class="text-xs font-semibold text-indigo-600 uppercase tracking-widest
                           hover:text-indigo-800 transition"
                >
                    {{ post.category }}
                </Link>

                <h1 class="mt-3 text-4xl font-bold leading-tight tracking-tight text-gray-900">
                    {{ post.title }}
                </h1>

                <!--<p class="mt-4 text-lg text-gray-500 leading-relaxed">
                    {{ post.description }}
                </p>-->

                <div class="mt-6 flex items-center gap-3">
                    <!-- Author avatar placeholder -->
                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center
                                text-indigo-600 font-semibold text-sm shrink-0">
                        {{ post.author_name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ post.author_name }}</p>
                        <time class="text-xs text-gray-400">{{ post.created_at }}</time>
                    </div>
                </div>
            </header>

            <!-- Tags -->
            <div v-if="post.tags?.length" class="mt-5 flex flex-wrap gap-2">
                <Link
                    v-for="tag in post.tags"
                    :key="tag.slug"
                    :href="route('tag.show', tag.slug)"
                    class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full
                           hover:bg-indigo-100 hover:text-indigo-700 transition"
                >
                    #{{ tag.name }}
                </Link>
            </div>

            <!-- Divider -->
            <hr class="my-8 border-gray-200" />

            <!-- Content -->
            <div
                class="prose prose-gray prose-lg max-w-none
                       prose-headings:font-bold prose-headings:tracking-tight
                       prose-a:text-indigo-600 prose-a:no-underline hover:prose-a:underline
                       prose-img:rounded-xl prose-img:shadow-md prose-img:mx-auto
                       prose-code:bg-gray-100 prose-code:px-1.5 prose-code:py-0.5
                       prose-code:rounded prose-code:text-sm prose-code:font-normal
                       prose-pre:bg-gray-950 prose-pre:text-gray-100
                       prose-blockquote:border-l-4 prose-blockquote:border-indigo-300
                       prose-blockquote:not-italic prose-blockquote:text-gray-600"
                v-html="post.content_html"
            />

            <!-- Pagination -->
            <div
                v-if="pagination"
                class="mt-12 flex items-center justify-between border-t border-gray-100 pt-8"
            >
                <Link
                    v-if="pagination.current_page > 1"
                    :href="route('blog.show', { blogPost: post.slug, page: pagination.current_page - 1 })"
                    class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-900 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Previous
                </Link>
                <span v-else />

                <span class="text-sm text-gray-400">
                    Page {{ pagination.current_page }} of {{ pagination.total_pages }}
                </span>

                <Link
                    v-if="pagination.current_page < pagination.total_pages"
                    :href="route('blog.show', { blogPost: post.slug, page: pagination.current_page + 1 })"
                    class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-900 transition"
                >
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </Link>
                <span v-else />
            </div>

            <!-- Footer -->
            <footer class="mt-16 py-8 border-t border-gray-100">
                <Link
                    :href="route('blog.index')"
                    class="text-sm text-gray-400 hover:text-gray-900 transition"
                >
                    ← Back to all posts
                </Link>
            </footer>

        </div>
    </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';

defineProps({
    post:       { type: Object, required: true },
    pagination: { type: Object, default: null },
});
</script>
