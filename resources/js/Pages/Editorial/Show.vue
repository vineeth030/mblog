<template>
    <Head>
        <title>{{ seoTitle }}</title>
        <meta head-key="description" name="description" :content="seoDescription">
        <link head-key="canonical" rel="canonical" :href="canonicalUrl">
        <meta head-key="og:title" property="og:title" :content="post.meta_title || post.title">
        <meta head-key="og:description" property="og:description" :content="seoDescription">
        <meta head-key="og:image" property="og:image" :content="post.featured_image_url || 'https://kambikutan.com/cover.jpg'">
        <meta head-key="og:url" property="og:url" :content="canonicalUrl">
        <meta head-key="og:type" property="og:type" content="article">
        <meta head-key="twitter:title" name="twitter:title" :content="post.meta_title || post.title">
        <meta head-key="twitter:description" name="twitter:description" :content="seoDescription">
        <meta head-key="twitter:image" name="twitter:image" :content="post.featured_image_url || 'https://kambikutan.com/cover.jpg'">
    </Head>

    <PublicLayout>

        <!-- Hero image -->
        <template #hero>
            <div
                v-if="post.featured_image_url"
                class="w-full bg-gray-100 max-h-[480px] overflow-hidden"
            >
                <img
                    :src="post.featured_image_url"
                    :alt="post.title"
                    class="w-full max-h-[480px] object-cover"
                />
            </div>
        </template>

        <div class="max-w-2xl">

            <!-- Back link -->
            <div class="pt-8">
                <Link
                    :href="route('editorial.index')"
                    class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-700 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    All editorials
                </Link>
            </div>

            <!-- Post header -->
            <header class="mt-6">
                <Link
                    :href="route('editorial.index')"
                    class="text-xs font-semibold text-indigo-600 uppercase tracking-widest
                           hover:text-indigo-800 transition"
                >
                    Editorial<span v-if="post.is_featured" class="ml-2 text-amber-600">★ Featured</span>
                </Link>

                <h1 class="mt-3 text-4xl font-bold leading-tight tracking-tight text-gray-900">
                    {{ post.title }}
                </h1>

                <p v-if="post.excerpt" class="mt-4 text-lg text-gray-500 leading-relaxed">
                    {{ post.excerpt }}
                </p>

                <div class="mt-6 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center
                                text-indigo-600 font-semibold text-sm shrink-0">
                        K
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Kambikutan Team</p>
                        <time class="text-xs text-gray-400">{{ post.published_at }}</time>
                    </div>
                </div>
            </header>

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

            <!-- Related editorial posts -->
            <section v-if="related?.length" class="mt-16 border-t border-gray-100 pt-10">
                <h2 class="text-lg font-bold text-gray-900 mb-6">More from Editorial</h2>
                <div class="divide-y divide-gray-100">
                    <article
                        v-for="r in related"
                        :key="r.slug"
                        class="py-6 flex gap-5 items-start"
                    >
                        <div class="flex-1 min-w-0">
                            <Link
                                :href="route('editorial.show', r.slug)"
                                class="block group"
                            >
                                <h3 class="text-base font-semibold text-gray-900 group-hover:text-indigo-600 transition line-clamp-2">
                                    {{ r.title }}
                                </h3>
                                <p v-if="r.excerpt" class="mt-1 text-sm text-gray-500 line-clamp-2">
                                    {{ r.excerpt }}
                                </p>
                            </Link>
                            <time class="mt-2 block text-xs text-gray-400">{{ r.published_at }}</time>
                        </div>
                        <Link
                            v-if="r.featured_image_url"
                            :href="route('editorial.show', r.slug)"
                            class="shrink-0 w-24 h-16 sm:w-28 sm:h-20 rounded-lg overflow-hidden bg-gray-100"
                        >
                            <img
                                :src="r.featured_image_url"
                                :alt="r.title"
                                class="w-full h-full object-cover hover:scale-105 transition duration-300"
                            />
                        </Link>
                    </article>
                </div>
            </section>

            <!-- Footer -->
            <footer class="mt-16 py-8 border-t border-gray-100">
                <Link
                    :href="route('editorial.index')"
                    class="text-sm text-gray-400 hover:text-gray-900 transition"
                >
                    ← Back to all editorials
                </Link>
            </footer>

        </div>
    </PublicLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';

const props = defineProps({
    post:    { type: Object, required: true },
    related: { type: Array,  default: () => [] },
});

const seoTitle = computed(() => props.post.meta_title || `${props.post.title} – Editorial | Kambikutan`);
const seoDescription = computed(
    () => props.post.meta_description || props.post.excerpt || `Read this editorial from the Kambikutan team.`,
);
const canonicalUrl = computed(() => route('editorial.show', props.post.slug));
</script>
