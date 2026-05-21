<template>
    <AdminLayout>

        <div class="flex items-center gap-4 mb-6">
            <Link
                :href="route('admin.story-submissions.index')"
                class="text-sm text-gray-400 hover:text-gray-600 transition flex items-center gap-1"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Submissions
            </Link>
            <span class="text-gray-300">/</span>
            <h1 class="text-xl font-semibold text-gray-900 truncate">{{ submission.title }}</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Details -->
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5 text-sm">
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Title</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ submission.title }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</dt>
                        <dd class="mt-1">
                            <a :href="`mailto:${submission.email}`" class="text-indigo-600 hover:text-indigo-800 transition">
                                {{ submission.email }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</dt>
                        <dd class="mt-1 text-gray-700">
                            <span v-if="submission.category">{{ submission.category }}</span>
                            <span v-else class="text-gray-400">—</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Current status</dt>
                        <dd class="mt-1"><StatusBadge :status="submission.status" /></dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tags</dt>
                        <dd class="mt-1">
                            <div v-if="tagList.length" class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="tag in tagList"
                                    :key="tag"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700"
                                >
                                    {{ tag }}
                                </span>
                            </div>
                            <span v-else class="text-gray-400">—</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Submitted</dt>
                        <dd class="mt-1 text-gray-700">{{ submission.created_at }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Last updated</dt>
                        <dd class="mt-1 text-gray-700">{{ submission.updated_at }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Story</dt>
                        <dd>
                            <pre
                                v-if="submission.story_content"
                                class="whitespace-pre-wrap break-words font-sans text-sm text-gray-800 leading-relaxed bg-gray-50 border border-gray-200 rounded-lg p-4 max-h-[32rem] overflow-y-auto"
                            >{{ submission.story_content }}</pre>
                            <span v-else class="text-gray-400">—</span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Actions -->
            <aside class="bg-white border border-gray-200 rounded-xl p-6 self-start">
                <h2 class="text-sm font-semibold text-gray-900 mb-3">Change status</h2>
                <div class="flex flex-col gap-2">
                    <button
                        v-for="s in statuses"
                        :key="s"
                        type="button"
                        :disabled="s === submission.status || statusForm.processing"
                        @click="updateStatus(s)"
                        class="w-full text-left px-3 py-2 rounded-lg border text-sm font-medium transition disabled:opacity-60 disabled:cursor-not-allowed"
                        :class="buttonClasses(s)"
                    >
                        <span class="inline-flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full" :class="dotClass(s)" />
                            {{ formatStatus(s) }}
                            <span v-if="s === submission.status" class="ml-auto text-xs text-gray-400">current</span>
                        </span>
                    </button>
                </div>

                <hr class="my-5 border-gray-100" />

                <button
                    type="button"
                    @click="deleteSubmission"
                    class="w-full px-3 py-2 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-sm font-medium transition"
                >
                    Delete submission
                </button>
            </aside>
        </div>

    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Admin/AdminLayout.vue';
import StatusBadge from '@/Components/Admin/StatusBadge.vue';

const props = defineProps({
    submission: { type: Object, required: true },
    statuses:   { type: Array,  required: true },
});

const statusForm = useForm({ status: props.submission.status });

const tagList = computed(() =>
    (props.submission.tags || '')
        .split(',')
        .map(t => t.trim())
        .filter(Boolean)
);

const DOTS = {
    pending:  'bg-amber-500',
    approved: 'bg-green-500',
    rejected: 'bg-red-500',
};

function dotClass(s) {
    return DOTS[s] ?? 'bg-gray-400';
}

function buttonClasses(s) {
    if (s === props.submission.status) return 'border-gray-200 bg-gray-50 text-gray-700';
    return 'border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300';
}

function formatStatus(s) {
    return s.charAt(0).toUpperCase() + s.slice(1);
}

function updateStatus(s) {
    statusForm.status = s;
    statusForm.patch(route('admin.story-submissions.status', props.submission.id), {
        preserveScroll: true,
    });
}

function deleteSubmission() {
    if (!confirm(`Delete submission "${props.submission.title}"? This cannot be undone.`)) return;
    router.delete(route('admin.story-submissions.destroy', props.submission.id));
}
</script>
