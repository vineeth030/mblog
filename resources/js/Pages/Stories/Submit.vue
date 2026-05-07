<template>
    <PublicLayout>

        <Head title="Submit Your Story" />

        <main class="max-w-2xl mx-auto px-6 py-12">

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Submit Your Story</h1>
                <p class="mt-2 text-sm text-gray-500">
                    Share your Malayalam story with us. Upload your manuscript as a PDF and we'll review it.
                </p>
            </div>

            <!-- Success message -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
            >
                <div
                    v-if="flash?.success"
                    class="mb-6 flex items-start gap-3 px-4 py-3 rounded-xl border border-green-200 bg-green-50 text-green-800 text-sm"
                >
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ flash.success }}</span>
                </div>
            </Transition>

            <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8">
                <form @submit.prevent="submit" class="flex flex-col gap-5">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="Story title"
                            :class="[inp, form.errors.title && inpErr]"
                            autofocus
                        />
                        <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="you@example.com"
                            :class="[inp, form.errors.email && inpErr]"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Story PDF</label>
                        <label
                            class="flex flex-col items-center justify-center w-full px-4 py-8 rounded-lg border-2 border-dashed cursor-pointer transition"
                            :class="form.errors.pdf_file
                                ? 'border-red-300 bg-red-50 hover:bg-red-100'
                                : 'border-gray-300 bg-gray-50 hover:bg-gray-100'"
                        >
                            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <span v-if="!form.pdf_file" class="text-sm text-gray-500">
                                <span class="font-medium text-indigo-600">Click to upload</span> a PDF file
                            </span>
                            <span v-else class="text-sm text-gray-700 font-medium truncate max-w-full">
                                {{ form.pdf_file.name }}
                            </span>
                            <span v-if="form.pdf_file" class="text-xs text-gray-400 mt-1">
                                {{ formatSize(form.pdf_file.size) }} — click to change
                            </span>
                            <span v-else class="text-xs text-gray-400 mt-1">PDF only · max 10 MB</span>
                            <input
                                type="file"
                                accept="application/pdf,.pdf"
                                class="hidden"
                                @change="onFileChange"
                            />
                        </label>
                        <div v-if="form.progress" class="mt-2 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div
                                class="bg-indigo-500 h-full transition-all"
                                :style="{ width: form.progress.percentage + '%' }"
                            />
                        </div>
                        <p v-if="form.errors.pdf_file" class="mt-1 text-xs text-red-600">{{ form.errors.pdf_file }}</p>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg
                                   bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed
                                   text-white text-sm font-semibold transition"
                        >
                            <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                            {{ form.processing ? 'Submitting…' : 'Submit Story' }}
                        </button>
                    </div>

                </form>
            </div>
        </main>

    </PublicLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';

const page  = usePage();
const flash = computed(() => page.props.flash);

const form = useForm({
    title:    '',
    email:    '',
    pdf_file: null,
});

const inp    = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition';
const inpErr = 'border-red-400 focus:ring-red-400 focus:border-red-400';

function onFileChange(e) {
    form.pdf_file = e.target.files?.[0] ?? null;
}

function formatSize(bytes) {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
}

function submit() {
    form.post(route('stories.submit.store'), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
}
</script>
