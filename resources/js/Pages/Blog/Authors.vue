<template>
    <Head>
        <title>Malayalam Story Authors – Writers on Kambikutan</title>
        <meta head-key="description" name="description" content="Browse all Malayalam story authors on Kambikutan and read the best kambikathakal written by each writer.">
        <link head-key="canonical" rel="canonical" href="https://kambikutan.com/authors">
        <meta head-key="og:title" property="og:title" content="Malayalam Story Authors – Kambikutan">
        <meta head-key="og:description" property="og:description" content="Browse all Malayalam story authors and read the best stories by each writer.">
        <meta head-key="og:url" property="og:url" content="https://kambikutan.com/authors">
        <meta head-key="og:type" property="og:type" content="website">
        <meta head-key="twitter:title" name="twitter:title" content="Malayalam Story Authors – Kambikutan">
        <meta head-key="twitter:description" name="twitter:description" content="Browse all Malayalam story authors and read the best stories by each writer.">
    </Head>

    <PublicLayout>
        <div>

            <!-- Breadcrumbs -->
            <div class="pt-8">
                <Breadcrumbs :items="breadcrumbs" />
            </div>

            <!-- Header -->
            <div class="py-12 border-b border-gray-100">
                <h1 class="text-4xl font-bold tracking-tight">Malayalam Story Authors</h1>
                <p class="mt-3 text-gray-500 leading-relaxed max-w-2xl">
                    Explore every writer on Kambikutan and read the best Malayalam stories by each author.
                </p>
            </div>

            <!-- Author list -->
            <div v-if="authors.length > 0" class="divide-y divide-gray-100">
                <article
                    v-for="author in authors"
                    :key="author.slug"
                    class="py-8"
                >
                    <Link
                        :href="route('author.show', author.slug)"
                        class="block group"
                    >
                        <h2 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition leading-snug">
                            {{ author.name }}
                        </h2>
                        <p v-if="author.bio" class="mt-2 text-gray-500 text-sm leading-relaxed line-clamp-2">
                            {{ author.bio }}
                        </p>
                        <p class="mt-3 text-xs text-gray-400">
                            {{ author.posts_count }} {{ author.posts_count === 1 ? 'story' : 'stories' }}
                        </p>
                    </Link>
                </article>
            </div>

            <!-- Empty state -->
            <div v-else class="py-24 text-center">
                <p class="text-gray-400 text-lg">No authors found.</p>
                <Link
                    :href="route('blog.index')"
                    class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800 transition"
                >
                    ← Browse all posts
                </Link>
            </div>

        </div>
    </PublicLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

defineProps({
    authors: { type: Array, required: true },
});

const breadcrumbs = [
    { title: 'Home', url: 'https://kambikutan.com/' },
    { title: 'Authors', url: 'https://kambikutan.com/authors' },
];
</script>
