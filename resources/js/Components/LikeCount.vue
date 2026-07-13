<template>
    <span class="inline-flex items-center gap-1" :title="`${formatted} likes`">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
        </svg>
        <span>{{ formatted }}<span v-if="showLabel"> likes</span></span>
        <span class="sr-only"> likes</span>
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    count:     { type: Number, default: 0 },
    showLabel: { type: Boolean, default: true },
});

// 1245 → "1,245" using the visitor's locale.
const formatted = computed(() => Number(props.count || 0).toLocaleString());
</script>
