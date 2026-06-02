<template>
    <div class="min-h-screen bg-gray-50">

        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-6xl mx-auto px-6 h-14 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <span class="font-bold text-gray-900 tracking-tight">mblog</span>
                    <nav class="hidden sm:flex items-center gap-4">
                        <Link
                            :href="route('admin.blog-posts.index')"
                            class="text-sm transition"
                            :class="isActive('admin.blog-posts') ? 'text-gray-900 font-medium' : 'text-gray-500 hover:text-gray-900'"
                        >
                            Posts
                        </Link>
                        <Link
                            :href="route('admin.editorial-posts.index')"
                            class="text-sm transition"
                            :class="isActive('admin.editorial-posts') ? 'text-gray-900 font-medium' : 'text-gray-500 hover:text-gray-900'"
                        >
                            Editorial
                        </Link>
                        <Link
                            :href="route('admin.categories.index')"
                            class="text-sm transition"
                            :class="isActive('admin.categories') ? 'text-gray-900 font-medium' : 'text-gray-500 hover:text-gray-900'"
                        >
                            Categories
                        </Link>
                        <Link
                            :href="route('admin.authors.index')"
                            class="text-sm transition"
                            :class="isActive('admin.authors') ? 'text-gray-900 font-medium' : 'text-gray-500 hover:text-gray-900'"
                        >
                            Authors
                        </Link>
                        <Link
                            :href="route('admin.story-submissions.index')"
                            class="text-sm transition"
                            :class="isActive('admin.story-submissions') ? 'text-gray-900 font-medium' : 'text-gray-500 hover:text-gray-900'"
                        >
                            Submissions
                        </Link>
                    </nav>
                </div>
                <button
                    type="button"
                    @click="logout"
                    class="text-sm text-gray-400 hover:text-red-600 transition"
                >
                    Logout
                </button>
            </div>
        </header>

        <!-- Flash toast -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div
                v-if="toast.message"
                class="fixed top-16 left-1/2 -translate-x-1/2 z-50 w-full max-w-sm px-4"
            >
                <div
                    class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm font-medium"
                    :class="toast.type === 'success'
                        ? 'bg-green-50 border-green-200 text-green-800'
                        : 'bg-red-50 border-red-200 text-red-800'"
                >
                    <div class="flex items-center gap-2">
                        <svg v-if="toast.type === 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg v-else class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ toast.message }}</span>
                    </div>
                    <button @click="toast.message = null" class="opacity-50 hover:opacity-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>

        <!-- Content -->
        <main class="max-w-6xl mx-auto px-6 py-8">
            <slot />
        </main>

    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

function isActive(prefix) {
    const current = route().current();
    return current ? current.startsWith(prefix) : false;
}

const page = usePage();
const toast = ref({ message: null, type: 'success' });
let timer = null;

watch(
    () => page.props.flash,
    (flash) => {
        const msg = flash?.success || flash?.error;
        if (!msg) return;
        toast.value = { message: msg, type: flash?.success ? 'success' : 'error' };
        clearTimeout(timer);
        timer = setTimeout(() => { toast.value.message = null; }, 4000);
    },
    { immediate: true, deep: true },
);

function logout() {
    router.post(route('admin.logout'));
}
</script>
