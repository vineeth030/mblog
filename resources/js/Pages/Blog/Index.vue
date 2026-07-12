<template>
    <Head>
        <title>Malayalam Kambi Stories – Latest Kambikathakal Daily | Kambikutan</title>
        <meta head-key="description" name="description" content="Read the latest Malayalam kambikathakal with new stories added daily. Explore romance, fantasy &amp; real-life kambi stories only on Kambikutan.">
        <link head-key="canonical" rel="canonical" href="https://kambikutan.com/">
        <meta head-key="og:title" property="og:title" content="Malayalam Kambi Stories – Kambikutan">
        <meta head-key="og:description" property="og:description" content="Read latest kambikathakal in Malayalam. New stories updated daily.">
        <meta head-key="og:image" property="og:image" content="https://kambikutan.com/cover.jpg">
        <meta head-key="og:url" property="og:url" content="https://kambikutan.com/">
        <meta head-key="og:type" property="og:type" content="website">
        <meta head-key="twitter:title" name="twitter:title" content="Malayalam Kambi Stories – Kambikutan">
        <meta head-key="twitter:description" name="twitter:description" content="Latest kambikathakal updated daily. Read now.">
        <meta head-key="twitter:image" name="twitter:image" content="https://kambikutan.com/cover.jpg">
    </Head>
    <PublicLayout>
        <div>

            <!-- Breadcrumbs (only on category-filtered listings) -->
            <div v-if="breadcrumbs" class="pt-8">
                <Breadcrumbs :items="breadcrumbs" />
            </div>

            <!-- Site intro -->
            <div class="py-12 border-b border-gray-100">
                <h1 class="text-4xl font-bold tracking-tight">
                    {{ currentCategory ? `Best Malayalam ${currentCategory} Stories` : 'Stories worth reading.' }}
                </h1>
                <!--<p class="mt-2 text-gray-500">Ideas, tutorials, and thoughts on web development.</p>-->
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
            <div v-if="posts.last_page > 1" class="py-8 border-t border-gray-100">
                <Pagination :paginator="posts" />
            </div>

        </div>
    </PublicLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ViewCount from '@/Components/ViewCount.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

defineProps({
    posts:           { type: Object,  required: true },
    currentCategory: { type: String,  default: null },
    breadcrumbs:     { type: Array,   default: null },
});
</script>
