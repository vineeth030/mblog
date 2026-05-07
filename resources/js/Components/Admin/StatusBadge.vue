<template>
    <span
        class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full"
        :class="classes"
    >
        <span class="w-1.5 h-1.5 rounded-full" :class="dotClass" />
        {{ label }}
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: { type: String, required: true },
});

const STYLES = {
    pending:  { wrap: 'bg-amber-100 text-amber-700',  dot: 'bg-amber-500'  },
    approved: { wrap: 'bg-green-100 text-green-700',  dot: 'bg-green-500'  },
    rejected: { wrap: 'bg-red-100 text-red-700',      dot: 'bg-red-500'    },
};

const classes  = computed(() => STYLES[props.status]?.wrap ?? 'bg-gray-100 text-gray-500');
const dotClass = computed(() => STYLES[props.status]?.dot  ?? 'bg-gray-400');
const label    = computed(() => props.status.charAt(0).toUpperCase() + props.status.slice(1));
</script>
