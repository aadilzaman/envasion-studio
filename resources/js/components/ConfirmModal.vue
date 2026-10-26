<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-slate-900/50 backdrop-blur-xs"
        @click.self="cancel"
    >
        <div class="relative w-full max-w-md p-6 bg-white border border-slate-200 rounded-2xl shadow-2xl transition-all">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0 text-xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">{{ title }}</h3>
                    <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">{{ message }}</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                <button
                    type="button"
                    @click="cancel"
                    :disabled="isLoading"
                    class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    @click="confirm"
                    :disabled="isLoading"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-100 transition disabled:opacity-50"
                >
                    <i v-if="isLoading" class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                    <span>{{ isLoading ? 'Deleting...' : confirmButtonText }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Confirm Deletion',
    },
    message: {
        type: String,
        default: 'Are you sure you want to proceed? This action cannot be undone.',
    },
    confirmButtonText: {
        type: String,
        default: 'Delete',
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['confirm', 'cancel']);

const confirm = () => {
    emit('confirm');
};

const cancel = () => {
    emit('cancel');
};
</script>
