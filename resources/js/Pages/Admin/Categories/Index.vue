<template>
    <AdminLayout>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Categories</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ categories.length }} categor{{ categories.length !== 1 ? 'ies' : 'y' }} total</p>
            </div>
            <Link
                :href="route('admin.categories.create')"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
                       text-sm font-medium px-4 py-2 rounded-lg transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Category
            </Link>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

            <div v-if="categories.length === 0" class="text-center py-16">
                <svg class="mx-auto w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/>
                </svg>
                <p class="mt-3 text-sm text-gray-500">No categories yet.</p>
                <Link :href="route('admin.categories.create')" class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Create your first category →
                </Link>
            </div>

            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3 hidden sm:table-cell">Posts</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="category in categories"
                        :key="category.id"
                        class="hover:bg-gray-50 transition"
                    >
                        <td class="px-4 py-3 font-medium text-gray-900">{{ category.name }}</td>
                        <td class="px-4 py-3 hidden sm:table-cell text-gray-500">{{ category.blog_posts_count }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link
                                :href="route('admin.categories.edit', category.id)"
                                class="text-indigo-600 hover:text-indigo-800 font-medium mr-4 transition"
                            >
                                Edit
                            </Link>
                            <button
                                type="button"
                                @click="deleteCategory(category)"
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
    categories: { type: Array, required: true },
});

function deleteCategory(category) {
    if (!confirm(`Delete category "${category.name}"? This cannot be undone.`)) return;
    router.delete(route('admin.categories.destroy', category.id));
}
</script>
