<template>
    <section v-if="stories.length" aria-labelledby="related-heading">
        <h2 id="related-heading" class="text-xl font-bold tracking-tight text-gray-900">
            Related Stories
        </h2>

        <div class="mt-6 grid gap-6 sm:grid-cols-2">
            <article
                v-for="story in stories"
                :key="story.slug"
                class="group"
            >
                <Link :href="route('blog.show', story.slug)" class="block">
                    <div
                        v-if="story.cover_image_url"
                        class="aspect-[16/9] rounded-lg overflow-hidden bg-gray-100 mb-3"
                    >
                        <img
                            :src="story.cover_image_url"
                            :alt="story.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                        />
                    </div>
                    <span v-if="story.category" class="text-xs font-medium text-indigo-600 uppercase tracking-wide">
                        {{ story.category }}
                    </span>
                    <h3 class="mt-1 font-bold text-gray-900 group-hover:text-indigo-600 transition leading-snug line-clamp-2">
                        {{ story.title }}
                    </h3>
                </Link>
                <div class="mt-2 flex items-center gap-2 text-xs text-gray-400">
                    <Link
                        v-if="story.author_slug"
                        :href="route('author.show', story.author_slug)"
                        class="font-medium text-gray-600 hover:text-indigo-600 transition"
                    >{{ story.author_name }}</Link>
                    <span v-else class="font-medium text-gray-600">{{ story.author_name }}</span>
                    <span>·</span>
                    <time>{{ story.created_at }}</time>
                    <span>·</span>
                    <ViewCount :count="story.views" />
                </div>
            </article>
        </div>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ViewCount from '@/Components/ViewCount.vue';

defineProps({
    stories: { type: Array, default: () => [] },
});
</script>
