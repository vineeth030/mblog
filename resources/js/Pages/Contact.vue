<template>
    <PublicLayout>

        <Head title="Contact Us" />

        <main class="max-w-2xl mx-auto px-6 py-12">

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Contact Us</h1>
                <p class="mt-2 text-sm text-gray-500">
                    We'd love to hear from you. Whether you have a question, feedback, or
                    just want to say hello, feel free to reach out.
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8">
                <p class="text-sm text-gray-600">
                    For any queries, suggestions, or concerns, please email us at:
                </p>
                <a
                    :href="`mailto:${email}`"
                    class="mt-3 inline-flex items-center gap-2 text-base font-semibold text-indigo-600 hover:text-indigo-700 transition"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ email }}
                </a>
                <p class="mt-4 text-xs text-gray-400">
                    We typically respond within a few days.
                </p>
            </div>
        </main>

    </PublicLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';

defineProps({
    email: { type: String, required: true },
});
</script>
