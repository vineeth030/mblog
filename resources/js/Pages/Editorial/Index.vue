<template>
    <Head>
        <title>Editorial – Official stories &amp; announcements | Kambikutan</title>
        <meta head-key="description" name="description" content="Curated editorial content from the Kambikutan team — product introductions, featured tools, company spotlights, service recommendations and announcements.">
        <link head-key="canonical" rel="canonical" :href="canonicalUrl">
        <meta head-key="og:title" property="og:title" content="Editorial – Kambikutan">
        <meta head-key="og:description" property="og:description" content="Official curated content from the Kambikutan team.">
        <meta head-key="og:image" property="og:image" content="https://kambikutan.com/cover.jpg">
        <meta head-key="og:url" property="og:url" :content="canonicalUrl">
        <meta head-key="og:type" property="og:type" content="website">
        <meta head-key="twitter:title" name="twitter:title" content="Editorial – Kambikutan">
        <meta head-key="twitter:description" name="twitter:description" content="Official curated content from the Kambikutan team.">
        <meta head-key="twitter:image" name="twitter:image" content="https://kambikutan.com/cover.jpg">
    </Head>
    <PublicLayout>
        <div>

            <!-- Section intro -->
            <div class="py-12 border-b border-gray-100">
                <span class="text-xs font-semibold text-indigo-600 uppercase tracking-widest">Editorial</span>
                <h1 class="mt-2 text-4xl font-bold tracking-tight">Official stories &amp; announcements.</h1>
                <p class="mt-2 text-gray-500 text-sm">Curated picks, product introductions and spotlights from our team.</p>
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
                            :href="route('editorial.show', post.slug)"
                            class="block group"
                        >
                            <span class="text-xs font-medium text-indigo-600 uppercase tracking-wide">
                                Editorial<span v-if="post.is_featured" class="ml-2 text-amber-600">★ Featured</span>
                            </span>
                            <h2 class="mt-1 text-xl font-bold text-gray-900 group-hover:text-indigo-600
                                       transition leading-snug line-clamp-2">
                                {{ post.title }}
                            </h2>
                            <p class="mt-2 text-gray-500 text-sm leading-relaxed line-clamp-2">
                                {{ post.excerpt }}
                            </p>
                        </Link>
                        <div class="mt-4 flex items-center gap-2 text-xs text-gray-400">
                            <span class="font-medium text-gray-600">Kambikutan Team</span>
                            <span>·</span>
                            <time>{{ post.published_at }}</time>
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <Link
                        v-if="post.featured_image_url"
                        :href="route('editorial.show', post.slug)"
                        class="shrink-0 w-28 h-20 sm:w-36 sm:h-24 rounded-lg overflow-hidden bg-gray-100"
                    >
                        <img
                            :src="post.featured_image_url"
                            :alt="post.title"
                            class="w-full h-full object-cover hover:scale-105 transition duration-300"
                        />
                    </Link>
                </article>
            </div>

            <!-- Empty state -->
            <div v-else class="py-24 text-center">
                <p class="text-gray-400 text-lg">No editorial posts yet.</p>
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

defineProps({
    posts: { type: Object, required: true },
});

const canonicalUrl = computed(() => route('editorial.index'));
</script>
