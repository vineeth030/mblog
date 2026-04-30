<template>
    <PublicLayout>
        <div class="max-w-3xl mx-auto px-6">

            <!-- Site intro -->
            <div class="py-12 border-b border-gray-100">
                <h1 class="text-4xl font-bold tracking-tight">Stories worth reading.</h1>
                <p class="mt-2 text-gray-500">Ideas, tutorials, and thoughts on web development.</p>
            </div>

            <!-- Category filter -->
            <div class="flex items-center gap-2 py-4 overflow-x-auto border-b border-gray-100 no-scrollbar">
                <Link
                    :href="route('blog.index')"
                    class="shrink-0 px-4 py-1.5 rounded-full text-sm transition"
                    :class="!currentCategory
                        ? 'bg-gray-900 text-white'
                        : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'"
                >
                    All
                </Link>
                <Link
                    v-for="cat in categories"
                    :key="cat"
                    :href="route('blog.index', { category: cat })"
                    class="shrink-0 px-4 py-1.5 rounded-full text-sm transition"
                    :class="currentCategory === cat
                        ? 'bg-gray-900 text-white'
                        : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'"
                >
                    {{ cat }}
                </Link>
            </div>

            <!-- Post list -->
            <div v-if="posts.data.length > 0" class="divide-y divide-gray-100">
                <article
                    v-for="post in posts.data"
                    :key="post.id"
                    class="py-8 flex gap-6 items-start"
                >
                    <!-- Text -->
                    <div class="flex-1 min-w-0">
                        <Link
                            :href="route('blog.show', post.id)"
                            class="block group"
                        >
                            <span class="text-xs font-medium text-indigo-600 uppercase tracking-wide">
                                {{ post.category }}
                            </span>
                            <h2 class="mt-1 text-xl font-bold text-gray-900 group-hover:text-indigo-600
                                       transition leading-snug line-clamp-2">
                                {{ post.title }}
                            </h2>
                            <p class="mt-2 text-gray-500 text-sm leading-relaxed line-clamp-2">
                                {{ post.description }}
                            </p>
                        </Link>
                        <div class="mt-4 flex items-center gap-2 text-xs text-gray-400">
                            <span class="font-medium text-gray-600">{{ post.author_name }}</span>
                            <span>·</span>
                            <time>{{ post.created_at }}</time>
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <Link
                        v-if="post.cover_image_url"
                        :href="route('blog.show', post.id)"
                        class="shrink-0 w-28 h-20 sm:w-36 sm:h-24 rounded-lg overflow-hidden bg-gray-100"
                    >
                        <img
                            :src="post.cover_image_url"
                            :alt="post.title"
                            class="w-full h-full object-cover hover:scale-105 transition duration-300"
                        />
                    </Link>
                </article>
            </div>

            <!-- Empty state -->
            <div v-else class="py-24 text-center">
                <p class="text-gray-400 text-lg">No posts found.</p>
                <Link
                    v-if="currentCategory"
                    :href="route('blog.index')"
                    class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800 transition"
                >
                    ← Clear filter
                </Link>
            </div>

            <!-- Pagination -->
            <div
                v-if="posts.last_page > 1"
                class="py-8 flex items-center justify-between border-t border-gray-100 text-sm"
            >
                <Link
                    v-if="posts.prev_page_url"
                    :href="posts.prev_page_url"
                    class="flex items-center gap-1 text-gray-500 hover:text-gray-900 transition"
                >
                    ← Newer
                </Link>
                <span v-else />
                <span class="text-gray-400">{{ posts.current_page }} / {{ posts.last_page }}</span>
                <Link
                    v-if="posts.next_page_url"
                    :href="posts.next_page_url"
                    class="flex items-center gap-1 text-gray-500 hover:text-gray-900 transition"
                >
                    Older →
                </Link>
                <span v-else />
            </div>

        </div>
    </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';

defineProps({
    posts:           { type: Object,  required: true },
    categories:      { type: Array,   default: () => [] },
    currentCategory: { type: String,  default: null },
});
</script>

<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
