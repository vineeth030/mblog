<template>
    <AdminLayout>

        <!-- Page header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Posts</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ posts.total }} post{{ posts.total !== 1 ? 's' : '' }} total</p>
            </div>
            <Link
                :href="route('admin.blog-posts.create')"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
                       text-sm font-medium px-4 py-2 rounded-lg transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Post
            </Link>
        </div>

        <!-- Table card -->
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

            <!-- Empty state -->
            <div v-if="posts.data.length === 0" class="text-center py-16">
                <svg class="mx-auto w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-3 text-sm text-gray-500">No posts yet.</p>
                <Link :href="route('admin.blog-posts.create')" class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Create your first post →
                </Link>
            </div>

            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3 w-14"></th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3 hidden md:table-cell">Category</th>
                        <th class="px-4 py-3 hidden sm:table-cell">Status</th>
                        <th class="px-4 py-3 hidden lg:table-cell">Date</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="post in posts.data"
                        :key="post.slug"
                        class="hover:bg-gray-50 transition"
                    >
                        <!-- Thumbnail -->
                        <td class="px-4 py-3">
                            <div class="w-10 h-10 rounded-md overflow-hidden bg-gray-100 shrink-0">
                                <img
                                    v-if="post.cover_image_url"
                                    :src="post.cover_image_url"
                                    class="w-full h-full object-cover"
                                    alt=""
                                />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01"/>
                                    </svg>
                                </div>
                            </div>
                        </td>

                        <!-- Title + author -->
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900 line-clamp-1">{{ post.title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">by {{ post.author_name }}</p>
                        </td>

                        <!-- Category -->
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="text-gray-500">{{ post.category }}</span>
                        </td>

                        <!-- Status badge -->
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <span
                                class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full"
                                :class="post.publish_status
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-500'"
                            >
                                <span
                                    class="w-1.5 h-1.5 rounded-full"
                                    :class="post.publish_status ? 'bg-green-500' : 'bg-gray-400'"
                                />
                                {{ post.publish_status ? 'Published' : 'Draft' }}
                            </span>
                        </td>

                        <!-- Date -->
                        <td class="px-4 py-3 hidden lg:table-cell text-gray-400 text-xs">
                            {{ post.created_at }}
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link
                                :href="route('admin.blog-posts.edit', post.slug)"
                                class="text-indigo-600 hover:text-indigo-800 font-medium mr-4 transition"
                            >
                                Edit
                            </Link>
                            <button
                                type="button"
                                @click="deletePost(post)"
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
        <div v-if="posts.last_page > 1" class="mt-5">
            <Pagination :paginator="posts" show-meta preserve-scroll />
        </div>

    </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Admin/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    posts: { type: Object, required: true },
});

function deletePost(post) {
    if (!confirm(`Delete "${post.title}"? This cannot be undone.`)) return;
    router.delete(route('admin.blog-posts.destroy', post.slug));
}
</script>
