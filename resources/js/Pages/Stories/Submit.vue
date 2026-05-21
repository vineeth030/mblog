<template>
    <PublicLayout>

        <Head title="Submit Your Story" />

        <main class="max-w-2xl mx-auto px-6 py-12">

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Submit Your Story</h1>
                <p class="mt-2 text-sm text-gray-500">
                    Share your Malayalam story with us. Write it below and we'll review it.
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select
                            v-model="form.category_id"
                            :class="[inp, form.errors.category_id && inpErr]"
                        >
                            <option value="" disabled>Select a category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.category_id" class="mt-1 text-xs text-red-600">{{ form.errors.category_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                        <input
                            v-model="form.tags"
                            type="text"
                            placeholder="e.g. അവിഹിതം, കൗമാരം, ചീറ്റിംഗ്"
                            :class="[inp, form.errors.tags && inpErr]"
                        />
                        <p class="mt-1 text-xs text-gray-400">Separate tags with commas.</p>
                        <p v-if="form.errors.tags" class="mt-1 text-xs text-red-600">{{ form.errors.tags }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Story</label>
                        <TextEditor
                            v-model="form.story_content"
                            :has-error="!!form.errors.story_content"
                            placeholder="Write your story here… (Markdown supported)"
                        />
                        <p v-if="form.errors.story_content" class="mt-1 text-xs text-red-600">{{ form.errors.story_content }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            What is {{ captcha.a }} + {{ captcha.b }}?
                        </label>
                        <input
                            v-model="form.captcha_answer"
                            type="number"
                            inputmode="numeric"
                            placeholder="Your answer"
                            :class="[inp, 'sm:max-w-xs', form.errors.captcha_answer && inpErr]"
                        />
                        <p class="mt-1 text-xs text-gray-400">A quick math check to keep out spam.</p>
                        <p v-if="form.errors.captcha_answer" class="mt-1 text-xs text-red-600">{{ form.errors.captcha_answer }}</p>
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
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';
import TextEditor   from '@/Components/Admin/TextEditor.vue';

const props = defineProps({
    categories: { type: Array,  required: true },
    captcha:    { type: Object, required: true },
});

const page  = usePage();
const flash = computed(() => page.props.flash);

const form = useForm({
    title:          '',
    email:          '',
    category_id:    '',
    tags:           '',
    story_content:  '',
    captcha_answer: '',
});

const inp    = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition';
const inpErr = 'border-red-400 focus:ring-red-400 focus:border-red-400';

function submit() {
    form.post(route('stories.submit.store'), {
        onSuccess: () => {
            form.reset();
            router.reload({ only: ['captcha'] });
        },
    });
}
</script>
