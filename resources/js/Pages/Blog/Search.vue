<template>
    <Head>
        <title>{{ query ? `Search results for "${query}"` : 'Search' }} | Kambikutan</title>
        <meta head-key="robots" name="robots" content="noindex, follow">
    </Head>
    <PublicLayout>
        <div>

            <!-- Search header -->
            <div class="py-12 border-b border-gray-100">
                <h1 class="text-4xl font-bold tracking-tight">
                    {{ query ? `Results for "${query}"` : 'Search stories' }}
                </h1>
                <form @submit.prevent="submit" class="mt-6 relative max-w-md">
                    <svg
                        class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input
                        v-model="term"
                        type="search"
                        placeholder="Search stories"
                        class="w-full pl-10 pr-3 py-2.5 text-sm rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    />
                </form>
            </div>

            <!-- No query yet -->
            <div v-if="!query" class="py-24 text-center">
                <p class="text-gray-400 text-lg">Type something to search.</p>
            </div>

            <!-- Post list -->
            <div v-else-if="posts.data.length > 0" class="divide-y divide-gray-100">
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
                            <span>·</span>
                            <LikeCount :count="post.likes" />
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
                <p class="text-gray-400 text-lg">No stories found for "{{ query }}".</p>
            </div>

            <!-- Pagination -->
            <div v-if="posts.last_page > 1" class="py-8 border-t border-gray-100">
                <Pagination :paginator="posts" />
            </div>

        </div>
    </PublicLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ViewCount from '@/Components/ViewCount.vue';
import LikeCount from '@/Components/LikeCount.vue';

const props = defineProps({
    posts: { type: Object, required: true },
    query: { type: String, default: '' },
});

const term = ref(props.query);

const submit = () => {
    const q = term.value.trim();
    if (!q) return;
    router.get(route('blog.search'), { q });
};
</script>
