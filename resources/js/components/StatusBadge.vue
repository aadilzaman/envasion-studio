<template>
    <span
        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold tracking-wide border transition-all"
        :class="badgeClasses"
    >
        <span class="w-1.5 h-1.5 rounded-full" :class="dotClasses"></span>
        {{ status }}
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
});

const badgeClasses = computed(() => {
    switch (props.status) {
        case 'Active':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'Completed':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'On Hold':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'In Progress':
            return 'bg-purple-50 text-purple-700 border-purple-200';
        case 'Pending':
        default:
            return 'bg-slate-100 text-slate-600 border-slate-200';
    }
});

const dotClasses = computed(() => {
    switch (props.status) {
        case 'Active':
            return 'bg-emerald-500 animate-pulse';
        case 'Completed':
            return 'bg-blue-500';
        case 'On Hold':
            return 'bg-amber-500';
        case 'In Progress':
            return 'bg-purple-500 animate-pulse';
        case 'Pending':
        default:
            return 'bg-slate-400';
    }
});
</script>
