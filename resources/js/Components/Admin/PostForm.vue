<template>
    <form @submit.prevent="$emit('submit')" enctype="multipart/form-data">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- ── Main content ──────────────────────────────── -->
            <div class="lg:col-span-2 flex flex-col gap-5">

                <!-- Title -->
                <div>
                    <label :class="lbl">Title</label>
                    <input
                        v-model="form.title"
                        type="text"
                        placeholder="Post title"
                        :class="[inp, 'text-base font-medium', form.errors.title && inpErr]"
                    />
                    <p v-if="form.errors.title" :class="err">{{ form.errors.title }}</p>
                </div>

                <!-- Slug -->
                <div>
                    <label :class="lbl">
                        Slug
                        <span class="text-gray-400 font-normal ml-1">(used in the URL)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-sm text-gray-400 pointer-events-none select-none">
                            /posts/
                        </span>
                        <input
                            v-model="form.slug"
                            type="text"
                            placeholder="my-awesome-post"
                            :class="[inp, 'pl-16', form.errors.slug && inpErr]"
                        />
                    </div>
                    <p v-if="form.errors.slug" :class="err">{{ form.errors.slug }}</p>
                </div>

                <!-- Description -->
                <div>
                    <label :class="lbl">
                        Short Description
                        <span class="text-gray-400 font-normal ml-1">(shown in listing)</span>
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        placeholder="A one-paragraph summary of the post…"
                        :class="[inp, 'resize-none', form.errors.description && inpErr]"
                    />
                    <p v-if="form.errors.description" :class="err">{{ form.errors.description }}</p>
                </div>

                <!-- Content editor -->
                <div>
                    <label :class="lbl">Content</label>
                    <TextEditor
                        v-model="form.content"
                        :has-error="!!form.errors.content"
                    />
                    <p v-if="form.errors.content" :class="err">{{ form.errors.content }}</p>
                </div>

            </div>

            <!-- ── Sidebar settings ──────────────────────────── -->
            <div class="flex flex-col gap-4">

                <!-- Save button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50
                           text-white text-sm font-semibold transition"
                >
                    {{ form.processing ? 'Saving…' : submitLabel }}
                </button>

                <!-- Settings card -->
                <div class="bg-white border border-gray-200 rounded-xl divide-y divide-gray-100">

                    <!-- Publish toggle -->
                    <div class="px-4 py-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Status</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ form.publish_status ? 'Published' : 'Draft' }}
                            </p>
                        </div>
                        <button
                            type="button"
                            role="switch"
                            :aria-checked="!!form.publish_status"
                            @click="form.publish_status = form.publish_status ? 0 : 1"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full
                                   border-2 border-transparent transition-colors duration-200
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            :class="form.publish_status ? 'bg-indigo-600' : 'bg-gray-200'"
                        >
                            <span
                                class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white
                                       shadow ring-0 transition-transform duration-200"
                                :class="form.publish_status ? 'translate-x-5' : 'translate-x-0'"
                            />
                        </button>
                    </div>

                    <!-- Category -->
                    <div class="px-4 py-3">
                        <label :class="lbl">Category</label>
                        <select
                            v-model="form.category_id"
                            :class="[inp, form.errors.category_id && inpErr]"
                        >
                            <option value="" disabled>Select a category…</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.category_id" :class="err">{{ form.errors.category_id }}</p>
                    </div>

                    <!-- Author -->
                    <div class="px-4 py-3">
                        <label :class="lbl">Author</label>
                        <select
                            v-model="form.author_id"
                            :class="[inp, form.errors.author_id && inpErr]"
                        >
                            <option value="" disabled>Select an author…</option>
                            <option v-for="author in authors" :key="author.id" :value="author.id">
                                {{ author.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.author_id" :class="err">{{ form.errors.author_id }}</p>
                    </div>

                    <!-- Cover image -->
                    <div class="px-4 py-3">
                        <label :class="lbl">Cover Image</label>

                        <!-- Preview -->
                        <div
                            v-if="preview || existingImageUrl"
                            class="mt-2 mb-3 relative rounded-lg overflow-hidden bg-gray-100 aspect-video"
                        >
                            <img
                                :src="preview || existingImageUrl"
                                class="w-full h-full object-cover"
                                alt="Cover preview"
                            />
                            <button
                                v-if="preview"
                                type="button"
                                @click="clearImage"
                                class="absolute top-2 right-2 bg-black/50 hover:bg-black/70 text-white
                                       rounded-full w-6 h-6 flex items-center justify-center transition"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Drop zone -->
                        <div
                            class="mt-2 flex flex-col items-center justify-center gap-1 border-2 border-dashed
                                   rounded-lg px-4 py-5 cursor-pointer transition text-center"
                            :class="form.errors.cover_image
                                ? 'border-red-400 bg-red-50'
                                : 'border-gray-300 hover:border-indigo-400 hover:bg-indigo-50'"
                            @click="fileInput.click()"
                        >
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0
                                         L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-xs text-gray-500">
                                {{ preview ? 'Replace image' : existingImageUrl ? 'Change image' : 'Upload cover image' }}
                            </span>
                            <span class="text-xs text-gray-400">JPG, PNG, WebP — max 2 MB</span>
                        </div>

                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/jpeg,image/png,image/gif,image/webp"
                            class="hidden"
                            @change="handleFile"
                        />
                        <p v-if="form.errors.cover_image" :class="err">{{ form.errors.cover_image }}</p>
                    </div>

                </div>
            </div>

        </div>
    </form>
</template>

<script setup>
import { ref } from 'vue';
import TextEditor from '@/Components/Admin/TextEditor.vue';

const props = defineProps({
    form:             { type: Object, required: true },
    existingImageUrl: { type: String, default: null  },
    submitLabel:      { type: String, default: 'Save Post' },
    categories:       { type: Array,  default: () => [] },
    authors:          { type: Array,  default: () => [] },
});

defineEmits(['submit']);

const fileInput = ref(null);
const preview   = ref(null);

// Class constants — avoids @apply (not supported in SFC <style> with Tailwind v4)
const lbl    = 'block text-sm font-medium text-gray-700 mb-1';
const inp    = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition';
const inpErr = 'border-red-400 focus:ring-red-400 focus:border-red-400';
const err    = 'mt-1 text-xs text-red-600';

function handleFile(e) {
    const file = e.target.files[0];
    if (!file) return;
    props.form.cover_image = file;
    const reader = new FileReader();
    reader.onload = (ev) => { preview.value = ev.target.result; };
    reader.readAsDataURL(file);
}

function clearImage() {
    preview.value = null;
    props.form.cover_image = null;
    if (fileInput.value) fileInput.value.value = '';
}
</script>
