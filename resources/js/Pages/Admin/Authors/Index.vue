<template>
    <AdminLayout>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Authors</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ authors.length }} author{{ authors.length !== 1 ? 's' : '' }} total</p>
            </div>
            <Link
                :href="route('admin.authors.create')"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
                       text-sm font-medium px-4 py-2 rounded-lg transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Author
            </Link>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

            <div v-if="authors.length === 0" class="text-center py-16">
                <svg class="mx-auto w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="mt-3 text-sm text-gray-500">No authors yet.</p>
                <Link :href="route('admin.authors.create')" class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Create your first author →
                </Link>
            </div>

            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3 hidden md:table-cell">Bio</th>
                        <th class="px-4 py-3 hidden sm:table-cell">Posts</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="author in authors"
                        :key="author.id"
                        class="hover:bg-gray-50 transition"
                    >
                        <td class="px-4 py-3 font-medium text-gray-900">{{ author.name }}</td>
                        <td class="px-4 py-3 hidden md:table-cell text-gray-500 max-w-xs truncate">
                            {{ author.bio || '—' }}
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-gray-500">{{ author.blog_posts_count }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link
                                :href="route('admin.authors.edit', author.id)"
                                class="text-indigo-600 hover:text-indigo-800 font-medium mr-4 transition"
                            >
                                Edit
                            </Link>
                            <button
                                type="button"
                                @click="deleteAuthor(author)"
                                class="text-red-500 hover:text-red-700 font-medium transition"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Admin/AdminLayout.vue';

defineProps({
    authors: { type: Array, required: true },
});

function deleteAuthor(author) {
    if (!confirm(`Delete author "${author.name}"? This cannot be undone.`)) return;
    router.delete(route('admin.authors.destroy', author.id));
}
</script>
