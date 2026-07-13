<template>
    <nav
        v-if="paginator.last_page > 1"
        aria-label="Pagination"
        class="flex flex-wrap items-center justify-between gap-3 text-sm"
    >
        <span v-if="showMeta" class="hidden sm:inline text-gray-500">
            Showing {{ paginator.from }}–{{ paginator.to }} of {{ paginator.total }}
        </span>

        <ul class="flex flex-wrap items-center gap-1 ml-auto">
            <li>
                <Link
                    v-if="paginator.prev_page_url"
                    :href="paginator.prev_page_url"
                    :preserve-scroll="preserveScroll"
                    rel="prev"
                    aria-label="Previous page"
                    class="inline-flex items-center justify-center h-9 px-3 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition"
                >
                    ←<span class="ml-1 hidden sm:inline">Prev</span>
                </Link>
                <span
                    v-else
                    aria-disabled="true"
                    class="inline-flex items-center justify-center h-9 px-3 rounded-lg border border-gray-100 text-gray-300 cursor-not-allowed"
                >
                    ←<span class="ml-1 hidden sm:inline">Prev</span>
                </span>
            </li>

            <li v-for="item in visiblePages" :key="item.key">
                <span
                    v-if="item.type === 'ellipsis'"
                    class="inline-flex items-center justify-center h-9 px-2 text-gray-400 select-none"
                >
                    …
                </span>
                <span
                    v-else-if="item.number === paginator.current_page"
                    aria-current="page"
                    class="inline-flex items-center justify-center min-w-9 h-9 px-3 rounded-lg bg-gray-100 font-semibold text-gray-900"
                >
                    {{ item.number }}
                </span>
                <Link
                    v-else
                    :href="pageUrl(item.number)"
                    :preserve-scroll="preserveScroll"
                    :aria-label="`Go to page ${item.number}`"
                    class="inline-flex items-center justify-center min-w-9 h-9 px-3 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition"
                >
                    {{ item.number }}
                </Link>
            </li>

            <li>
                <Link
                    v-if="paginator.next_page_url"
                    :href="paginator.next_page_url"
                    :preserve-scroll="preserveScroll"
                    rel="next"
                    aria-label="Next page"
                    class="inline-flex items-center justify-center h-9 px-3 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition"
                >
                    <span class="mr-1 hidden sm:inline">Next</span>→
                </Link>
                <span
                    v-else
                    aria-disabled="true"
                    class="inline-flex items-center justify-center h-9 px-3 rounded-lg border border-gray-100 text-gray-300 cursor-not-allowed"
                >
                    <span class="mr-1 hidden sm:inline">Next</span>→
                </span>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    paginator:      { type: Object,  required: true },
    showMeta:       { type: Boolean, default: false },
    preserveScroll: { type: Boolean, default: false },
});

const pageUrl = (n) => {
    const template = props.paginator.first_page_url || props.paginator.path || '';
    if (/[?&]page=\d+/.test(template)) {
        return template.replace(/([?&])page=\d+/, `$1page=${n}`);
    }
    const sep = template.includes('?') ? '&' : '?';
    return `${template}${sep}page=${n}`;
};

// How many consecutive page numbers to show in the sliding window:
// fewer on mobile so the row doesn't wrap, more on desktop.
const WINDOW_MOBILE  = 5;
const WINDOW_DESKTOP = 8;

// Track viewport against Tailwind's `sm` breakpoint (640px). Guard against SSR
// where `window` is undefined; default to the desktop window in that case.
const isMobile = ref(false);
let mql = null;
const syncViewport = () => { isMobile.value = mql.matches; };

onMounted(() => {
    if (typeof window === 'undefined') return;
    mql = window.matchMedia('(max-width: 639.98px)');
    syncViewport();
    mql.addEventListener('change', syncViewport);
});
onUnmounted(() => {
    if (mql) mql.removeEventListener('change', syncViewport);
});

const visiblePages = computed(() => {
    const current = props.paginator.current_page;
    const last    = props.paginator.last_page;
    if (last <= 1) return [];

    // Build a window of consecutive pages centered on the current page,
    // clamped to the [1, last] range so it stays full-width at the edges.
    const WINDOW = isMobile.value ? WINDOW_MOBILE : WINDOW_DESKTOP;
    const half  = Math.floor(WINDOW / 2);
    let   start = Math.max(1, current - half);
    let   end   = Math.min(last, start + WINDOW - 1);
    start = Math.max(1, end - WINDOW + 1);

    const wanted = new Set([1, last]);
    for (let p = start; p <= end; p++) wanted.add(p);
    const sorted = [...wanted].filter(p => p >= 1 && p <= last).sort((a, b) => a - b);

    const result = [];
    let prev = 0;
    for (const p of sorted) {
        if (p - prev > 1) result.push({ type: 'ellipsis', key: `e${prev}` });
        result.push({ type: 'page', number: p, key: `p${p}` });
        prev = p;
    }
    return result;
});
</script>
