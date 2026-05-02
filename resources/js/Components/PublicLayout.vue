<template>
    <div class="min-h-screen bg-white text-gray-900">

        <!-- Header -->
        <header class="border-b border-gray-100">
            <div class="max-w-3xl mx-auto px-6 h-14 flex items-center justify-between">
                <Link :href="route('blog.index')" class="font-bold text-xl tracking-tight hover:opacity-70 transition">
                    kambikutan.com
                </Link>
                <!--<Link
                    :href="route('admin.login')"
                    class="text-xs text-gray-400 hover:text-gray-700 transition"
                >
                    Admin →
                </Link>-->
            </div>
        </header>

        <!-- Page content -->
        <slot />

    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
