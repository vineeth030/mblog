<template>
    <section
        v-if="parts.length > 1"
        aria-labelledby="series-heading"
        class="rounded-xl border transition-colors duration-300 motion-reduce:transition-none p-4"
        :class="isReadingMode ? 'border-white/10' : 'border-gray-200'"
    >
        <h2
            id="series-heading"
            class="text-xs font-semibold uppercase tracking-widest"
            :class="isReadingMode ? 'text-gray-400' : 'text-gray-500'"
        >
            Parts in this story
        </h2>
        <ol class="mt-3 space-y-1.5 text-sm">
            <li v-for="part in parts" :key="part.slug">
                <span
                    v-if="part.is_current"
                    class="font-semibold"
                    :class="isReadingMode ? 'text-white' : 'text-gray-900'"
                >Part {{ part.part_number }}: {{ part.title }}</span>
                <Link
                    v-else
                    :href="route('blog.show', part.slug)"
                    class="text-indigo-500 hover:text-indigo-400 transition"
                    :class="isReadingMode ? '' : 'text-indigo-600 hover:text-indigo-800'"
                >Part {{ part.part_number }}: {{ part.title }}</Link>
            </li>
        </ol>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    parts:         { type: Array,   default: () => [] },
    isReadingMode: { type: Boolean, default: false },
});
</script>
