<template>
    <button
        type="button"
        @click="toggle"
        :disabled="pending"
        :aria-pressed="liked"
        :aria-label="liked ? 'Unlike this story' : 'Like this story'"
        class="group inline-flex items-center gap-2 rounded-full border px-5 py-2.5 text-sm font-semibold
               transition-colors duration-200 motion-reduce:transition-none
               focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-rose-400
               disabled:opacity-60 disabled:cursor-not-allowed"
        :class="liked
            ? 'border-rose-200 bg-rose-50 text-rose-600'
            : 'border-gray-200 bg-white text-gray-600 hover:border-rose-200 hover:text-rose-600'"
    >
        <svg
            class="w-5 h-5 transition-transform duration-200 motion-reduce:transition-none"
            :class="[liked ? 'scale-110' : 'group-hover:scale-110', bump ? 'animate-heart-bump' : '']"
            :fill="liked ? 'currentColor' : 'none'"
            stroke="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            />
        </svg>
        <span>{{ liked ? 'Liked' : 'Like' }}</span>
        <span
            v-if="count > 0"
            class="inline-flex items-center justify-center min-w-6 px-1.5 h-6 rounded-full text-xs font-bold"
            :class="liked ? 'bg-rose-100 text-rose-600' : 'bg-gray-100 text-gray-500'"
        >
            {{ formattedCount }}
        </span>
    </button>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    postSlug:     { type: String,  required: true },
    initialLikes: { type: Number,  default: 0 },
    initialLiked: { type: Boolean, default: false },
});

const liked   = ref(props.initialLiked);
const count   = ref(Math.max(0, props.initialLikes));
const pending = ref(false);
const bump    = ref(false);

const formattedCount = computed(() => Number(count.value || 0).toLocaleString());

// localStorage backs up the "liked" UI state so the heart stays filled even if
// the visitor cookie is later cleared. The server counter remains authoritative.
const STORAGE_KEY = 'liked_stories';

const readLikedSet = () => {
    try {
        return new Set(JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'));
    } catch {
        return new Set();
    }
};

const persistLiked = (isLiked) => {
    try {
        const set = readLikedSet();
        isLiked ? set.add(props.postSlug) : set.delete(props.postSlug);
        localStorage.setItem(STORAGE_KEY, JSON.stringify([...set]));
    } catch {
        /* storage unavailable (private mode / quota) — non-fatal */
    }
};

onMounted(() => {
    // Trust the server, but fall back to local memory when it didn't recognise us.
    if (!liked.value && readLikedSet().has(props.postSlug)) {
        liked.value = true;
    }
});

// Laravel accepts the (decrypted) XSRF-TOKEN cookie echoed back as a header.
const xsrfToken = () => {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
};

async function toggle() {
    if (pending.value) return;
    pending.value = true;

    const next = !liked.value;

    // Optimistic update for instant feedback.
    liked.value = next;
    count.value = Math.max(0, count.value + (next ? 1 : -1));
    if (next) {
        bump.value = false;
        requestAnimationFrame(() => { bump.value = true; });
    }

    try {
        const res = await fetch(route('blog.like', props.postSlug), {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': xsrfToken(),
            },
        });
        if (!res.ok) throw new Error(`Like failed: ${res.status}`);

        // Reconcile with the server's authoritative state.
        const data = await res.json();
        liked.value = data.liked;
        count.value = Math.max(0, data.likes);
        persistLiked(data.liked);
    } catch {
        // Revert the optimistic change on failure.
        liked.value = !next;
        count.value = Math.max(0, count.value + (next ? -1 : 1));
    } finally {
        pending.value = false;
    }
}
</script>

<style scoped>
@keyframes heart-bump {
    0%   { transform: scale(1); }
    30%  { transform: scale(1.35); }
    60%  { transform: scale(0.92); }
    100% { transform: scale(1.1); }
}
.animate-heart-bump {
    animation: heart-bump 0.35s ease-in-out;
}
@media (prefers-reduced-motion: reduce) {
    .animate-heart-bump {
        animation: none;
    }
}
</style>
