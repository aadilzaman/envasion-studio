<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-slate-950/80 backdrop-blur-sm"
        @click.self="close"
    >
        <div class="relative w-full max-w-md p-6 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-100">
                        {{ isEdit ? 'Edit Task' : 'Add New Task' }}
                    </h3>
                </div>
                <button
                    @click="close"
                    class="p-1.5 text-slate-400 hover:text-slate-200 hover:bg-slate-800 rounded-lg transition"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Error Banner -->
            <div v-if="generalError" class="mt-4 p-3 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-sm">
                {{ generalError }}
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="mt-5 space-y-4">
                <!-- Title -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                        Task Title <span class="text-rose-400">*</span>
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        placeholder="e.g. Implement user authentication"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-950 border rounded-xl text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm"
                        :class="errors.title ? 'border-rose-500/60' : 'border-slate-800'"
                    />
                    <p v-if="errors.title" class="mt-1 text-xs text-rose-400">{{ errors.title[0] }}</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                        Description
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        placeholder="Details or deliverables for this task..."
                        class="w-full px-3.5 py-2.5 bg-slate-950 border rounded-xl text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm"
                        :class="errors.description ? 'border-rose-500/60' : 'border-slate-800'"
                    ></textarea>
                    <p v-if="errors.description" class="mt-1 text-xs text-rose-400">{{ errors.description[0] }}</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                        Status <span class="text-rose-400">*</span>
                    </label>
                    <select
                        v-model="form.status"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-950 border rounded-xl text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm"
                        :class="errors.status ? 'border-rose-500/60' : 'border-slate-800'"
                    >
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                    <p v-if="errors.status" class="mt-1 text-xs text-rose-400">{{ errors.status[0] }}</p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button
                        type="button"
                        @click="close"
                        class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-xl transition"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 transition disabled:opacity-50"
                    >
                        <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ isSubmitting ? 'Saving...' : (isEdit ? 'Update Task' : 'Add Task') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { apiService } from '../services/api';
import { useToast } from '../services/toast';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    projectId: {
        type: [Number, String],
        required: true,
    },
    task: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'saved']);
const { success, error } = useToast();

const isEdit = computed(() => !!props.task?.id);
const isSubmitting = ref(false);
const generalError = ref('');
const errors = ref({});

const form = ref({
    title: '',
    description: '',
    status: 'Pending',
});

watch(
    () => props.task,
    (newVal) => {
        if (newVal) {
            form.value = {
                title: newVal.title || '',
                description: newVal.description || '',
                status: newVal.status || 'Pending',
            };
        } else {
            form.value = {
                title: '',
                description: '',
                status: 'Pending',
            };
        }
        errors.value = {};
        generalError.value = '';
    },
    { immediate: true }
);

const close = () => {
    emit('close');
};

const handleSubmit = async () => {
    isSubmitting.value = true;
    errors.value = {};
    generalError.value = '';

    try {
        let result;
        if (isEdit.value) {
            result = await apiService.updateTask(props.task.id, form.value);
            success('Task updated successfully!');
        } else {
            result = await apiService.createTask(props.projectId, form.value);
            success('Task created successfully!');
        }
        emit('saved', result.data);
        close();
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
        } else {
            generalError.value = err.response?.data?.message || 'An error occurred while saving the task.';
            error(generalError.value);
        }
    } finally {
        isSubmitting.value = false;
    }
};
</script>
