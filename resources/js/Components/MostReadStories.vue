<template>
    <section v-if="stories.length" aria-labelledby="most-read-heading">
        <p id="most-read-heading" class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">
            Most Read Stories
        </p>
        <ol class="space-y-1">
            <li v-for="(story, i) in stories" :key="story.slug">
                <Link
                    :href="route('blog.show', story.slug)"
                    class="group flex items-start gap-2 px-3 py-2 rounded-md text-sm transition
                           text-gray-600 hover:text-gray-900 hover:bg-gray-100"
                >
                    <span class="shrink-0 w-4 text-xs font-semibold text-gray-300 group-hover:text-indigo-500 tabular-nums">
                        {{ i + 1 }}
                    </span>
                    <span class="min-w-0">
                        <span class="block line-clamp-2 leading-snug">{{ story.title }}</span>
                        <ViewCount :count="story.views" class="mt-0.5 text-xs text-gray-400" />
                    </span>
                </Link>
            </li>
        </ol>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ViewCount from '@/Components/ViewCount.vue';

const page = usePage();

const stories = computed(() => page.props.mostReadStories ?? []);
</script>
