<template>
    <Head>
        <title>{{ h1 }} | Kambikutan</title>
        <meta head-key="description" name="description" :content="`Read the best Malayalam ${category.name} stories and kambikathakal on Kambikutan, updated regularly.`">
        <link head-key="canonical" rel="canonical" :href="canonicalUrl">
        <meta head-key="og:title" property="og:title" :content="h1">
        <meta head-key="og:description" property="og:description" :content="`Read the best Malayalam ${category.name} stories on Kambikutan.`">
        <meta head-key="og:url" property="og:url" :content="canonicalUrl">
        <meta head-key="og:type" property="og:type" content="website">
        <meta head-key="twitter:title" name="twitter:title" :content="h1">
        <meta head-key="twitter:description" name="twitter:description" :content="`Read the best Malayalam ${category.name} stories on Kambikutan.`">
    </Head>

    <PublicLayout>
        <div>

            <!-- Breadcrumbs -->
            <div v-if="breadcrumbs" class="pt-8">
                <Breadcrumbs :items="breadcrumbs" />
            </div>

            <!-- Header -->
            <div class="py-12 border-b border-gray-100">
                <h1 class="text-4xl font-bold tracking-tight">{{ h1 }}</h1>
            </div>

            <!-- Post list -->
            <div v-if="posts.data.length > 0" class="divide-y divide-gray-100">
                <article
                    v-for="post in posts.data"
                    :key="post.slug"
                    class="py-8 flex gap-6 items-start"
                >
                    <!-- Text -->
                    <div class="flex-1 min-w-0">
                        <Link
                            :href="route('blog.show', post.slug)"
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
                            <Link
                                v-if="post.author_slug"
                                :href="route('author.show', post.author_slug)"
                                class="font-medium text-gray-600 hover:text-indigo-600 transition"
                            >{{ post.author_name }}</Link>
                            <span v-else class="font-medium text-gray-600">{{ post.author_name }}</span>
                            <span>·</span>
                            <time>{{ post.created_at }}</time>
                            <span>·</span>
                            <ViewCount :count="post.views" />
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <Link
                        v-if="post.cover_image_url"
                        :href="route('blog.show', post.slug)"
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
                <p class="text-gray-400 text-lg">No stories found in this category.</p>
                <Link
                    :href="route('blog.index')"
                    class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800 transition"
                >
                    ← Browse all posts
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="posts.last_page > 1" class="py-8 border-t border-gray-100">
                <Pagination :paginator="posts" />
            </div>

        </div>
    </PublicLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ViewCount from '@/Components/ViewCount.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const props = defineProps({
    category:    { type: Object, required: true },
    posts:       { type: Object, required: true },
    breadcrumbs: { type: Array,  default: null },
});

const h1 = computed(() => `Best Malayalam ${props.category.name} Stories`);
const canonicalUrl = computed(() => route('category.show', props.category.slug));
</script>
