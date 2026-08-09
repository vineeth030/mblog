<template>
    <div class="relative">
        <div
            v-if="selectedLabel"
            class="flex items-center justify-between gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm"
        >
            <span class="truncate text-gray-900">{{ selectedLabel }}</span>
            <button
                type="button"
                @click="clear"
                class="text-gray-400 hover:text-gray-700 transition leading-none shrink-0"
                aria-label="Clear previous part"
            >&times;</button>
        </div>

        <input
            v-else
            v-model="query"
            type="search"
            placeholder="Search by title…"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900
                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            @input="debouncedSearch"
        />

        <ul
            v-if="results.length"
            class="absolute z-10 mt-1 w-full max-h-56 overflow-auto rounded-lg border border-gray-200
                   bg-white shadow-lg text-sm divide-y divide-gray-100"
        >
            <li v-for="result in results" :key="result.id">
                <button
                    type="button"
                    @click="select(result)"
                    class="block w-full text-left px-3 py-2 hover:bg-indigo-50 transition truncate"
                >
                    {{ result.title }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue:   { type: [Number, String], default: null },
    initialLabel: { type: String, default: null },
    excludeId:    { type: [Number, String], default: null },
});

const emit = defineEmits(['update:modelValue']);

const query = ref('');
const results = ref([]);
const selectedLabel = ref(props.initialLabel);
let debounceTimer = null;

function debouncedSearch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(search, 300);
}

async function search() {
    const term = query.value.trim();
    if (term.length < 2) {
        results.value = [];
        return;
    }

    const params = new URLSearchParams({ q: term });
    if (props.excludeId) params.set('exclude', props.excludeId);

    const response = await fetch(route('admin.blog-posts.search-parts') + '?' + params.toString());
    results.value = await response.json();
}

function select(result) {
    selectedLabel.value = result.title;
    results.value = [];
    query.value = '';
    emit('update:modelValue', result.id);
}

function clear() {
    selectedLabel.value = null;
    emit('update:modelValue', null);
}

watch(() => props.initialLabel, (label) => { selectedLabel.value = label; });
</script>
