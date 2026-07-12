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

        <!-- Footer (hidden in reading mode) -->
        <footer v-show="!isReadingMode" class="border-t border-gray-100 mt-8">
            <div class="max-w-5xl mx-auto px-6 py-12">
                <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">

                    <!-- Brand + description -->
                    <div class="lg:col-span-1">
                        <Link :href="route('blog.index')" class="font-bold text-lg tracking-tight hover:opacity-70 transition">
                            kambikutan.com
                        </Link>
                        <p class="mt-3 text-sm text-gray-500 leading-relaxed">
                            Read the latest Malayalam kambi stories and kambikathakal, with new stories added daily.
                        </p>
                    </div>

                    <!-- Explore -->
                    <nav aria-label="Explore">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Explore</p>
                        <ul class="space-y-2 text-sm">
                            <li><Link :href="route('blog.index')" class="text-gray-600 hover:text-gray-900 transition">Home</Link></li>
                            <li><Link :href="route('blog.most-read')" class="text-gray-600 hover:text-gray-900 transition">Most Read Stories</Link></li>
                            <li><Link :href="route('author.index')" class="text-gray-600 hover:text-gray-900 transition">Authors</Link></li>
                        </ul>
                    </nav>

                    <!-- Site -->
                    <nav aria-label="Site">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Site</p>
                        <ul class="space-y-2 text-sm">
                            <li><Link :href="route('stories.submit')" class="text-gray-600 hover:text-gray-900 transition">Submit Story</Link></li>
                            <li><Link :href="route('contact')" class="text-gray-600 hover:text-gray-900 transition">Contact</Link></li>
                            <li><Link :href="route('privacy')" class="text-gray-600 hover:text-gray-900 transition">Privacy Policy</Link></li>
                            <li><Link :href="route('terms')" class="text-gray-600 hover:text-gray-900 transition">Terms &amp; Conditions</Link></li>
                        </ul>
                    </nav>

                    <!-- Categories -->
                    <nav v-if="categories.length" aria-label="Categories">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Categories</p>
                        <ul class="space-y-2 text-sm">
                            <li v-for="cat in categories" :key="cat.slug">
                                <Link
                                    :href="route('category.show', cat.slug)"
                                    class="text-gray-600 hover:text-gray-900 transition"
                                >
                                    {{ cat.name }}
                                </Link>
                            </li>
                        </ul>
                    </nav>

                </div>

                <!-- Copyright -->
                <div class="mt-10 pt-6 border-t border-gray-100 text-xs text-gray-400">
                    © {{ year }} kambikutan.com. All rights reserved.
                </div>
            </div>
        </footer>

    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import CategoryList from '@/Components/CategoryList.vue';
import MostReadStories from '@/Components/MostReadStories.vue';
import { useReadingMode } from '@/composables/useReadingMode';

const { isReadingMode } = useReadingMode();

const page = usePage();
const categories = computed(() => page.props.publicCategories ?? []);
const year = new Date().getFullYear();
</script>
