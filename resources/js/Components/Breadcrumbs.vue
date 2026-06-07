<template>
    <!-- Skip rendering on shallow trails (e.g. the bare home page). -->
    <template v-if="items.length > 1">
        <!-- Schema.org BreadcrumbList — generated from the same `items` as the
             visible trail, injected into <head>. Uses <component :is="'script'">
             to sidestep the SFC "<script> in template" parsing pitfall. -->
        <Head>
            <component
                :is="'script'"
                type="application/ld+json"
                head-key="breadcrumb-jsonld"
                v-html="jsonLd"
            />
        </Head>

        <!-- Visible, crawlable breadcrumb navigation. -->
        <nav aria-label="Breadcrumb" class="text-sm">
            <ol class="flex flex-wrap items-center gap-1.5 text-gray-400">
                <li
                    v-for="(item, i) in items"
                    :key="i"
                    class="flex items-center gap-1.5"
                >
                    <Link
                        v-if="item.url && i < items.length - 1"
                        :href="item.url"
                        class="hover:text-gray-700 transition"
                    >
                        {{ item.title }}
                    </Link>
                    <span
                        v-else
                        :aria-current="i === items.length - 1 ? 'page' : undefined"
                        class="text-gray-600 font-medium truncate max-w-[60vw] sm:max-w-xs"
                    >
                        {{ item.title }}
                    </span>

                    <svg
                        v-if="i < items.length - 1"
                        class="w-3.5 h-3.5 text-gray-300 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </li>
            </ol>
        </nav>
    </template>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    // Ordered trail, root → current. Each: { title: string, url?: string|null }.
    // The final item is the current page and is rendered as plain text.
    items: { type: Array, default: () => [] },
});

// Build a Schema.org BreadcrumbList from the same source as the visible trail.
// Every position gets an `item` URL (including the current page's canonical),
// per Google's recommendation for a complete list.
const jsonLd = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: props.items.map((item, i) => ({
            '@type': 'ListItem',
            position: i + 1,
            name: item.title,
            ...(item.url ? { item: item.url } : {}),
        })),
    }),
);
</script>
