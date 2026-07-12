<template>
    <div
        class="min-h-screen transition-colors duration-300 motion-reduce:transition-none"
        :class="isReadingMode ? 'bg-[#111111] text-[#e5e5e5]' : 'bg-white text-gray-900'"
    >

        <!-- Header -->
        <header
            class="border-b transition-colors duration-300 motion-reduce:transition-none"
            :class="isReadingMode ? 'border-white/10' : 'border-gray-100'"
        >
            <div class="max-w-5xl mx-auto px-6 h-14 flex items-center justify-between">
                <Link :href="route('blog.index')" class="font-bold text-xl tracking-tight hover:opacity-70 transition">
                    kambikutan.com
                </Link>
                <nav class="flex items-center gap-4">
                    <Link
                        :href="route('author.index')"
                        class="text-xs text-gray-400 transition"
                        :class="isReadingMode ? 'hover:text-white' : 'hover:text-gray-700'"
                    >
                        Authors
                    </Link>
                    <Link
                        :href="route('contact')"
                        class="text-xs text-gray-400 transition"
                        :class="isReadingMode ? 'hover:text-white' : 'hover:text-gray-700'"
                    >
                        Contact
                    </Link>
                    <Link
                        :href="route('stories.submit')"
                        class="text-xs px-3 py-2 rounded-md transition"
                        :class="isReadingMode
                            ? 'bg-white text-black hover:bg-gray-200'
                            : 'bg-black text-white hover:text-gray-700'"
                    >
                        Submit Story →
                    </Link>
                </nav>
            </div>
        </header>

        <!-- Optional full-width hero (above the two-column grid) -->
        <slot name="hero" />

        <!-- Two-column grid: sidebar + content (desktop), single column (mobile).
             Reading mode collapses to a single centered column and hides the
             category navigation. -->
        <div class="max-w-5xl mx-auto px-6">
            <div :class="isReadingMode ? '' : 'lg:grid lg:grid-cols-[220px_minmax(0,1fr)] lg:gap-10'">

                <!-- Sidebar (desktop only) -->
                <aside v-show="!isReadingMode" class="hidden lg:block pt-8">
                    <div class="sticky top-6 space-y-8">
                        <CategoryList />
                        <MostReadStories />
                    </div>
                </aside>

                <!-- Page content -->
                <div class="min-w-0">
                    <slot />
                </div>
            </div>

            <!-- Mobile categories (bottom of page) -->
            <div v-show="!isReadingMode" class="lg:hidden border-t border-gray-100 py-10 space-y-8">
                <CategoryList />
                <MostReadStories />
            </div>
        </div>

    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import CategoryList from '@/Components/CategoryList.vue';
import MostReadStories from '@/Components/MostReadStories.vue';
import { useReadingMode } from '@/composables/useReadingMode';

const { isReadingMode } = useReadingMode();
</script>
