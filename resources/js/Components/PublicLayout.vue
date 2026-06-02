<template>
    <div class="min-h-screen bg-white text-gray-900">

        <!-- Header -->
        <header class="border-b border-gray-100">
            <div class="max-w-5xl mx-auto px-6 h-14 flex items-center justify-between">
                <Link :href="route('blog.index')" class="font-bold text-xl tracking-tight hover:opacity-70 transition">
                    kambikutan.com
                </Link>
                <nav class="flex items-center gap-4">
                    <Link
                        :href="route('contact')"
                        class="text-xs text-gray-400 hover:text-gray-700 transition"
                    >
                        Contact
                    </Link>
                    <Link
                        :href="route('stories.submit')"
                        class="text-xs text-gray-400 hover:text-gray-700 transition"
                    >
                        Submit Story →
                    </Link>
                </nav>
            </div>
        </header>

        <!-- Optional full-width hero (above the two-column grid) -->
        <slot name="hero" />

        <!-- Two-column grid: sidebar + content (desktop), single column (mobile) -->
        <div class="max-w-5xl mx-auto px-6">
            <div class="lg:grid lg:grid-cols-[220px_minmax(0,1fr)] lg:gap-10">

                <!-- Sidebar (desktop only) -->
                <aside class="hidden lg:block pt-8">
                    <div class="sticky top-6">
                        <CategoryList />
                    </div>
                </aside>

                <!-- Page content -->
                <div class="min-w-0">
                    <slot />
                </div>
            </div>

            <!-- Mobile categories (bottom of page) -->
            <div class="lg:hidden border-t border-gray-100 py-10">
                <CategoryList />
            </div>
        </div>

    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import CategoryList from '@/Components/CategoryList.vue';
</script>
