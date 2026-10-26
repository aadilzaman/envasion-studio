<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-slate-900/50 backdrop-blur-xs"
        @click.self="close"
    >
        <div class="relative w-full max-w-md p-6 bg-white border border-slate-200 rounded-2xl shadow-2xl transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center">
                        <i :class="isEdit ? 'fa-solid fa-pen-to-square' : 'fa-solid fa-list-check'"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            {{ isEdit ? 'Edit Task' : 'Add New Task' }}
                        </h3>
                        <p class="text-xs text-slate-500">Configure deliverable requirements</p>
                    </div>
                </div>
                <button
                    @click="close"
                    class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition"
                >
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Error Banner -->
            <div v-if="generalError" class="mt-4 p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                <span>{{ generalError }}</span>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="mt-5 space-y-4">
                <!-- Title -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Task Title <span class="text-rose-500">*</span>
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        placeholder="e.g. Implement user authentication"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm"
                        :class="errors.title ? 'border-rose-400' : 'border-slate-200'"
                    />
                    <p v-if="errors.title" class="mt-1 text-xs text-rose-600">{{ errors.title[0] }}</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Description
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        placeholder="Details or deliverables for this task..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm"
                        :class="errors.description ? 'border-rose-400' : 'border-slate-200'"
                    ></textarea>
                    <p v-if="errors.description" class="mt-1 text-xs text-rose-600">{{ errors.description[0] }}</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <select
                        v-model="form.status"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm cursor-pointer"
                        :class="errors.status ? 'border-rose-400' : 'border-slate-200'"
                    >
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                    <p v-if="errors.status" class="mt-1 text-xs text-rose-600">{{ errors.status[0] }}</p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button
                        type="button"
                        @click="close"
                        class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-100 transition disabled:opacity-50"
                    >
                        <i v-if="isSubmitting" class="fa-solid fa-circle-notch fa-spin text-sm"></i>
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
