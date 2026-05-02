<template>
    <AdminLayout>

        <div class="flex items-center gap-4 mb-6">
            <Link
                :href="route('admin.authors.index')"
                class="text-sm text-gray-400 hover:text-gray-600 transition flex items-center gap-1"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Authors
            </Link>
            <span class="text-gray-300">/</span>
            <h1 class="text-xl font-semibold text-gray-900">{{ author.name }}</h1>
        </div>

        <div class="max-w-lg bg-white border border-gray-200 rounded-xl p-6">
            <form @submit.prevent="submit" class="flex flex-col gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        :class="[inp, form.errors.name && inpErr]"
                        autofocus
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Bio
                        <span class="text-gray-400 font-normal ml-1">(optional)</span>
                    </label>
                    <textarea
                        v-model="form.bio"
                        rows="4"
                        :class="[inp, 'resize-none', form.errors.bio && inpErr]"
                    />
                    <p v-if="form.errors.bio" class="mt-1 text-xs text-red-600">{{ form.errors.bio }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50
                               text-white text-sm font-semibold transition"
                    >
                        {{ form.processing ? 'Saving…' : 'Update Author' }}
                    </button>
                    <Link
                        :href="route('admin.authors.index')"
                        class="text-sm text-gray-500 hover:text-gray-700 transition"
                    >
                        Cancel
                    </Link>
                </div>
            </form>
        </div>

    </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Admin/AdminLayout.vue';

const props = defineProps({
    author: { type: Object, required: true },
});

const form = useForm({
    name: props.author.name,
    bio:  props.author.bio ?? '',
});

const inp    = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition';
const inpErr = 'border-red-400 focus:ring-red-400 focus:border-red-400';

function submit() {
    form.put(route('admin.authors.update', props.author.id));
}
</script>
