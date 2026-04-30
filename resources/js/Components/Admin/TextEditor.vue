<template>
    <div
        class="rounded-lg border overflow-hidden transition"
        :class="hasError
            ? 'border-red-400 focus-within:ring-2 focus-within:ring-red-400'
            : 'border-gray-300 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500'"
    >
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center gap-0.5 px-2 py-1.5 bg-gray-50 border-b border-gray-200">
            <button
                v-for="btn in toolbar"
                :key="btn.label"
                type="button"
                :title="btn.title"
                @click="apply(btn)"
                class="px-2 py-1 rounded text-xs font-mono text-gray-600 hover:bg-gray-200 hover:text-gray-900 transition select-none"
            >
                {{ btn.label }}
            </button>
            <div class="w-px h-4 bg-gray-300 mx-1"/>
            <span class="text-xs text-gray-400 ml-auto">Markdown</span>
        </div>

        <!-- Textarea -->
        <textarea
            ref="el"
            :value="modelValue"
            :placeholder="placeholder"
            rows="18"
            @input="emit('update:modelValue', $event.target.value)"
            class="w-full px-4 py-3 text-sm text-gray-800 leading-relaxed resize-y bg-white focus:outline-none font-sans"
        />
    </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';

const props = defineProps({
    modelValue:  { type: String,  default: '' },
    placeholder: { type: String,  default: 'Write your content here… (Markdown supported)' },
    hasError:    { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);
const el   = ref(null);

const toolbar = [
    { label: 'B',    title: 'Bold',        before: '**',    after: '**',      ph: 'bold text'   },
    { label: 'I',    title: 'Italic',      before: '*',     after: '*',       ph: 'italic text' },
    { label: 'H2',   title: 'Heading 2',   before: '## ',   after: '',        ph: 'Heading'     },
    { label: 'H3',   title: 'Heading 3',   before: '### ',  after: '',        ph: 'Subheading'  },
    { label: '❝',    title: 'Blockquote',  before: '\n> ',  after: '',        ph: 'Quote'       },
    { label: '`',    title: 'Inline code', before: '`',     after: '`',       ph: 'code'        },
    { label: '```',  title: 'Code block',  before: '```\n', after: '\n```',   ph: 'code'        },
    { label: '---',  title: 'Divider',     before: '\n---\n',after: '',       ph: ''            },
    { label: '🔗',   title: 'Link',        before: '[',     after: '](https://)', ph: 'link text' },
];

function apply(btn) {
    const textarea = el.value;
    const s = textarea.selectionStart;
    const e = textarea.selectionEnd;
    const val = props.modelValue;
    const selected = val.slice(s, e) || btn.ph;
    const next = val.slice(0, s) + btn.before + selected + btn.after + val.slice(e);
    emit('update:modelValue', next);
    nextTick(() => {
        textarea.focus();
        const start = s + btn.before.length;
        textarea.setSelectionRange(start, start + selected.length);
    });
}
</script>
