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
            return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30';
        case 'Completed':
            return 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30';
        case 'On Hold':
            return 'bg-amber-500/10 text-amber-400 border-amber-500/30';
        case 'In Progress':
            return 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30';
        case 'Pending':
        default:
            return 'bg-slate-500/10 text-slate-400 border-slate-500/30';
    }
});

const dotClasses = computed(() => {
    switch (props.status) {
        case 'Active':
            return 'bg-emerald-400 animate-pulse';
        case 'Completed':
            return 'bg-cyan-400';
        case 'On Hold':
            return 'bg-amber-400';
        case 'In Progress':
            return 'bg-indigo-400 animate-pulse';
        case 'Pending':
        default:
            return 'bg-slate-400';
    }
});
</script>
