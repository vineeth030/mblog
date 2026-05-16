<template>
    <nav :aria-label="'Categories'">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">
            Categories
        </p>
        <ul class="space-y-1">
            <li>
                <Link
                    :href="route('blog.index')"
                    class="flex items-center justify-between px-3 py-2 rounded-md text-sm transition"
                    :class="onBlogIndex && !currentCategory
                        ? 'bg-gray-900 text-white'
                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                >
                    <span>All</span>
                </Link>
            </li>
            <li v-for="cat in categories" :key="cat.name">
                <Link
                    :href="route('blog.index', { category: cat.name })"
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-md text-sm transition"
                    :class="onBlogIndex && currentCategory === cat.name
                        ? 'bg-gray-900 text-white'
                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                >
                    <span class="truncate">{{ cat.name }}</span>
                    <span
                        class="shrink-0 text-xs"
                        :class="onBlogIndex && currentCategory === cat.name ? 'text-gray-300' : 'text-gray-400'"
                    >
                        {{ cat.count }}
                    </span>
                </Link>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

const categories = computed(() => page.props.publicCategories ?? []);
const currentCategory = computed(() => page.props.currentCategory ?? null);
const onBlogIndex = computed(() => route().current('blog.index'));
</script>
