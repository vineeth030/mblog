<template>
    <AdminLayout>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Story Submissions</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ submissions.total }} submission{{ submissions.total !== 1 ? 's' : '' }} total
                </p>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                </svg>
                <input
                    v-model="search"
                    @input="debouncedFilter"
                    type="search"
                    placeholder="Search by title or email…"
                    class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
            </div>
            <select
                v-model="status"
                @change="applyFilters"
                class="px-3 py-2 text-sm rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            >
                <option value="">All statuses</option>
                <option v-for="s in statuses" :key="s" :value="s">
                    {{ formatStatus(s) }}
                </option>
            </select>
        </div>

        <!-- Table card -->
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

            <div v-if="submissions.data.length === 0" class="text-center py-16">
                <svg class="mx-auto w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-3 text-sm text-gray-500">No submissions found.</p>
            </div>

            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3 hidden md:table-cell">Email</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 hidden lg:table-cell">Submitted</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="submission in submissions.data"
                        :key="submission.id"
                        class="hover:bg-gray-50 transition"
                    >
                        <td class="px-4 py-3 font-medium text-gray-900 max-w-xs">
                            <p class="line-clamp-1">{{ submission.title }}</p>
                            <p class="md:hidden text-xs text-gray-400 mt-0.5">{{ submission.email }}</p>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-gray-500">{{ submission.email }}</td>
                        <td class="px-4 py-3">
                            <StatusBadge :status="submission.status" />
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-gray-400 text-xs">
                            {{ submission.created_at }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link
                                :href="route('admin.story-submissions.show', submission.id)"
                                class="text-indigo-600 hover:text-indigo-800 font-medium mr-4 transition"
                            >
                                View
                            </Link>
                            <button
                                type="button"
                                @click="deleteSubmission(submission)"
                                class="text-red-500 hover:text-red-700 font-medium transition"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="submissions.last_page > 1"
            class="mt-5 flex items-center justify-between text-sm text-gray-500"
        >
            <span>Showing {{ submissions.from }}–{{ submissions.to }} of {{ submissions.total }}</span>
            <div class="flex items-center gap-1">
                <Link
                    v-if="submissions.prev_page_url"
                    :href="submissions.prev_page_url"
                    preserve-scroll
                    class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 transition"
                >
                    ← Prev
                </Link>
                <span class="px-3 py-1.5 text-gray-600">
                    {{ submissions.current_page }} / {{ submissions.last_page }}
                </span>
                <Link
                    v-if="submissions.next_page_url"
                    :href="submissions.next_page_url"
                    preserve-scroll
                    class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 transition"
                >
                    Next →
                </Link>
            </div>
        </div>

    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Admin/AdminLayout.vue';
import StatusBadge from '@/Components/Admin/StatusBadge.vue';

const props = defineProps({
    submissions: { type: Object, required: true },
    filters:     { type: Object, required: true },
    statuses:    { type: Array,  required: true },
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

let debounceTimer = null;
function debouncedFilter() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(applyFilters, 300);
}

function applyFilters() {
    router.get(
        route('admin.story-submissions.index'),
        {
            search: search.value || undefined,
            status: status.value || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function deleteSubmission(submission) {
    if (!confirm(`Delete submission "${submission.title}"? This cannot be undone.`)) return;
    router.delete(route('admin.story-submissions.destroy', submission.id), {
        preserveScroll: true,
    });
}

function formatStatus(s) {
    return s.charAt(0).toUpperCase() + s.slice(1);
}
</script>
