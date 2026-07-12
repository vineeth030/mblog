<template>
    <Head>
        <title>{{ seoTitle }}</title>
        <meta head-key="description" name="description" :content="seoDescription">
        <link head-key="canonical" rel="canonical" :href="canonicalUrl">
        <meta head-key="og:title" property="og:title" :content="post.title">
        <meta head-key="og:description" property="og:description" :content="post.description">
        <meta head-key="og:image" property="og:image" :content="post.cover_image_url || 'https://kambikutan.com/cover.jpg'">
        <meta head-key="og:url" property="og:url" :content="canonicalUrl">
        <meta head-key="og:type" property="og:type" content="article">
        <meta head-key="twitter:title" name="twitter:title" :content="post.title">
        <meta head-key="twitter:description" name="twitter:description" :content="post.description">
        <meta head-key="twitter:image" name="twitter:image" :content="post.cover_image_url || 'https://kambikutan.com/cover.jpg'">
    </Head>

    <PublicLayout>

        <!-- Hero image (full-width, above two-column grid) -->
        <template #hero>
            <div
                v-if="post.cover_image_url && !isReadingMode"
                class="w-full bg-gray-100 max-h-[480px] overflow-hidden"
            >
                <img
                    :src="post.cover_image_url"
                    :alt="post.title"
                    class="w-full max-h-[480px] object-cover"
                />
            </div>
        </template>

        <div :class="isReadingMode ? 'max-w-[760px] mx-auto' : 'max-w-2xl'">

            <!-- Reading-mode toggle: fixed so it stays reachable in both modes -->
            <button
                type="button"
                @click="toggle"
                :aria-pressed="isReadingMode"
                aria-label="Toggle reading mode"
                class="fixed top-4 right-4 z-50 inline-flex items-center gap-2 rounded-full border
                       px-4 py-2 text-sm font-medium shadow-sm
                       transition-colors duration-300 motion-reduce:transition-none
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500"
                :class="isReadingMode
                    ? 'bg-white/10 border-white/20 text-[#e5e5e5] hover:bg-white/20 focus-visible:ring-offset-[#111111]'
                    : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>{{ isReadingMode ? 'Exit reading mode' : 'Reading mode' }}</span>
            </button>

            <!-- Breadcrumbs (visible trail + Schema.org JSON-LD) -->
            <div class="pt-8">
                <Breadcrumbs :items="breadcrumbs" />
            </div>

            <!-- Post header -->
            <header class="mt-6">
                <Link
                    :href="route('blog.index', { category: post.category })"
                    class="text-xs font-semibold text-indigo-600 uppercase tracking-widest
                           hover:text-indigo-800 transition"
                >
                    {{ post.category }}
                </Link>

                <h1
                    class="mt-3 text-4xl font-bold leading-tight tracking-tight transition-colors duration-300 motion-reduce:transition-none"
                    :class="isReadingMode ? 'text-white' : 'text-gray-900'"
                >
                    {{ post.title }}
                </h1>

                <!--<p class="mt-4 text-lg text-gray-500 leading-relaxed">
                    {{ post.description }}
                </p>-->

                <div class="mt-6 flex items-center gap-3">
                    <!-- Author avatar placeholder -->
                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center
                                text-indigo-600 font-semibold text-sm shrink-0">
                        {{ post.author_name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <Link
                            v-if="post.author_slug"
                            :href="route('author.show', post.author_slug)"
                            class="text-sm font-semibold transition hover:text-indigo-600"
                            :class="isReadingMode ? 'text-white' : 'text-gray-900'"
                        >{{ post.author_name }}</Link>
                        <p
                            v-else
                            class="text-sm font-semibold"
                            :class="isReadingMode ? 'text-white' : 'text-gray-900'"
                        >{{ post.author_name }}</p>
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <time>{{ post.created_at }}</time>
                            <span>·</span>
                            <ViewCount :count="post.views" />
                        </div>
                    </div>
                </div>
            </header>

            <!-- Tags -->
            <div v-if="post.tags?.length" class="mt-5 flex flex-wrap gap-2">
                <Link
                    v-for="tag in post.tags"
                    :key="tag.slug"
                    :href="route('tag.show', tag.slug)"
                    class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full
                           hover:bg-indigo-100 hover:text-indigo-700 transition"
                >
                    #{{ tag.name }}
                </Link>
            </div>

            <!-- Divider -->
            <hr
                class="my-8 transition-colors duration-300 motion-reduce:transition-none"
                :class="isReadingMode ? 'border-white/10' : 'border-gray-200'"
            />

            <!-- Content — always rendered in the DOM (SEO-safe); reading mode only
                 restyles it via prose-invert + larger type + looser leading. -->
            <div
                ref="contentEl"
                class="prose prose-gray max-w-none
                       prose-headings:font-bold prose-headings:tracking-tight
                       prose-a:text-indigo-600 prose-a:no-underline hover:prose-a:underline
                       prose-img:rounded-xl prose-img:shadow-md prose-img:mx-auto
                       prose-code:px-1.5 prose-code:py-0.5
                       prose-code:rounded prose-code:text-sm prose-code:font-normal
                       prose-pre:bg-gray-950 prose-pre:text-gray-100
                       prose-blockquote:border-l-4 prose-blockquote:border-indigo-300
                       prose-blockquote:not-italic
                       transition-colors duration-300 motion-reduce:transition-none"
                :class="isReadingMode
                    ? 'prose-invert prose-xl !leading-loose prose-code:bg-white/10'
                    : 'prose-lg prose-code:bg-gray-100 prose-blockquote:text-gray-600'"
                v-html="post.content_html"
            />

            <!-- Pagination — hidden in reading mode (closest thing to a
                 "related/next posts" distraction on this page). -->
            <div v-if="pagination && !isReadingMode" class="mt-12 border-t border-gray-100 pt-8">
                <Pagination :paginator="pagination" />
            </div>

            <!-- Related stories — hidden in reading mode to avoid distraction. -->
            <div v-if="!isReadingMode && relatedPosts.length" class="mt-16 pt-8 border-t border-gray-100">
                <RelatedStories :stories="relatedPosts" />
            </div>

            <!-- Footer -->
            <footer
                class="mt-16 py-8 border-t transition-colors duration-300 motion-reduce:transition-none"
                :class="isReadingMode ? 'border-white/10' : 'border-gray-100'"
            >
                <Link
                    :href="route('blog.index')"
                    class="text-sm text-gray-400 transition"
                    :class="isReadingMode ? 'hover:text-white' : 'hover:text-gray-900'"
                >
                    ← Back to all posts
                </Link>
            </footer>

        </div>
    </PublicLayout>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ViewCount from '@/Components/ViewCount.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import RelatedStories from '@/Components/RelatedStories.vue';
import { useReadingMode } from '@/composables/useReadingMode';

const props = defineProps({
    post:         { type: Object, required: true },
    pagination:   { type: Object, default: null },
    breadcrumbs:  { type: Array,  default: () => [] },
    relatedPosts: { type: Array,  default: () => [] },
});

// ── Reading mode ──────────────────────────────────────────────────────────
const { isReadingMode, toggle, restore, disable, deactivate } = useReadingMode();

function handleReadingModeKeys(e) {
    if (e.key === 'Escape' && isReadingMode.value) disable();
}

const seoTitle = computed(() => `${props.post.title} – Malayalam erotic stories`);
const seoDescription = computed(
    () => `Read ${props.post.description} in Malayalam. A romantic and thrilling story series. Continue reading now.`,
);
const canonicalUrl = computed(() => route('blog.show', props.post.slug));

// ── Copy-trap: append source attribution to anything copied from the story ──
// Invisible to readers; only rewrites clipboard contents at copy time.
const contentEl = ref(null);

function handleCopy(e) {
    const selection = window.getSelection();
    if (!selection || selection.isCollapsed) return;

    // Only act when the selection actually lives inside the story content.
    if (!contentEl.value || !contentEl.value.contains(selection.anchorNode)) return;

    const selectedText = selection.toString();
    if (!selectedText.trim()) return;

    const url = canonicalUrl.value;
    const textNote = `\n\n— Source: ${url} © kambikutan.com`;
    const htmlNote = `<br><br>— Source: <a href="${url}">${url}</a> © kambikutan.com`;

    e.clipboardData.setData('text/plain', selectedText + textNote);
    e.clipboardData.setData(
        'text/html',
        `${selectedText.replace(/\n/g, '<br>')}${htmlNote}`,
    );
    e.preventDefault();
}

onMounted(() => {
    restore(); // re-apply the saved preference on (re)load of an article
    document.addEventListener('copy', handleCopy);
    document.addEventListener('keydown', handleReadingModeKeys);
});

onBeforeUnmount(() => {
    // Drop the active state (without clearing the saved preference) so reading
    // mode doesn't bleed onto the index/contact pages when navigating away.
    deactivate();
    document.removeEventListener('copy', handleCopy);
    document.removeEventListener('keydown', handleReadingModeKeys);
});
</script>
