<template>
    <div class="space-y-6">
        <!-- Breadcrumbs Navigation -->
        <nav class="flex items-center gap-2 text-xs text-slate-400">
            <router-link to="/" class="hover:text-slate-200 transition">Dashboard</router-link>
            <span>/</span>
            <router-link to="/projects" class="hover:text-slate-200 transition">Projects</router-link>
            <span>/</span>
            <span class="text-indigo-400 font-medium truncate max-w-xs">{{ project?.title || 'Details' }}</span>
        </nav>

        <!-- Loading Skeleton -->
        <div v-if="loading && !project" class="space-y-6 animate-pulse">
            <div class="p-8 bg-slate-900/60 border border-slate-800 rounded-2xl space-y-4">
                <div class="h-8 w-1/2 bg-slate-800 rounded"></div>
                <div class="h-4 w-3/4 bg-slate-800/80 rounded"></div>
                <div class="h-6 w-32 bg-slate-800 rounded"></div>
            </div>
            <div class="p-6 bg-slate-900/60 border border-slate-800 rounded-2xl space-y-4">
                <div class="h-6 w-40 bg-slate-800 rounded"></div>
                <div class="h-12 w-full bg-slate-800/60 rounded"></div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="p-8 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-center space-y-4">
            <p class="text-rose-400 font-semibold text-base">{{ error }}</p>
            <div class="flex items-center justify-center gap-3">
                <button
                    @click="fetchProject"
                    class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-xl"
                >
                    Retry
                </button>
                <router-link
                    to="/projects"
                    class="px-4 py-2 text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 rounded-xl"
                >
                    Back to Projects
                </router-link>
            </div>
        </div>

        <!-- Project Loaded Content -->
        <div v-else-if="project" class="space-y-6">
            <!-- Project Overview Banner Card -->
            <div class="p-6 sm:p-8 bg-slate-900/80 border border-slate-800 rounded-2xl shadow-xl space-y-6">
                <!-- Header row -->
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                                {{ project.title }}
                            </h1>
                            <StatusBadge :status="project.status" />
                        </div>
                        <p class="text-sm text-slate-300 max-w-3xl leading-relaxed whitespace-pre-line">
                            {{ project.description || 'No detailed project description has been specified.' }}
                        </p>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                        <button
                            @click="isEditProjectOpen = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-200 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <span>Edit Project</span>
                        </button>
                        <button
                            @click="isDeleteProjectOpen = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 rounded-xl transition"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Delete</span>
                        </button>
                    </div>
                </div>

                <!-- Metrics and Deadline strip -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-800">
                    <div class="p-3 bg-slate-950/60 border border-slate-800/80 rounded-xl">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Tasks</span>
                        <p class="text-xl font-extrabold text-white mt-0.5">{{ project.tasks_count || 0 }}</p>
                    </div>
                    <div class="p-3 bg-slate-950/60 border border-slate-800/80 rounded-xl">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400">Completed</span>
                        <p class="text-xl font-extrabold text-cyan-400 mt-0.5">{{ project.completed_tasks_count || 0 }}</p>
                    </div>
                    <div class="p-3 bg-slate-950/60 border border-slate-800/80 rounded-xl">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Pending</span>
                        <p class="text-xl font-extrabold text-amber-400 mt-0.5">{{ pendingCount }}</p>
                    </div>
                    <div class="p-3 bg-slate-950/60 border border-slate-800/80 rounded-xl">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Target Deadline</span>
                        <p class="text-sm font-semibold text-slate-200 mt-1">
                            {{ project.deadline ? formatDate(project.deadline) : 'None' }}
                        </p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div>
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-400 mb-2">
                        <span>Project Completion Progress</span>
                        <span class="text-indigo-400 font-mono">{{ completionPercentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-950 rounded-full h-2.5 overflow-hidden p-0.5 border border-slate-800">
                        <div
                            class="bg-gradient-to-r from-indigo-500 via-purple-500 to-cyan-400 h-1.5 rounded-full transition-all duration-500"
                            :style="{ width: completionPercentage + '%' }"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- TASK MANAGEMENT SECTION -->
            <div class="p-6 sm:p-8 bg-slate-900/80 border border-slate-800 rounded-2xl shadow-xl space-y-6">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
                    <div>
                        <h2 class="text-xl font-bold text-slate-100 flex items-center gap-2">
                            <span>Project Deliverables & Tasks</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-600/20 text-indigo-400 border border-indigo-500/30">
                                {{ filteredTasks.length }}
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Manage, track, and complete milestones for this client deliverable.
                        </p>
                    </div>

                    <!-- Right Controls -->
                    <div class="flex items-center gap-3">
                        <button
                            @click="openCreateTaskModal"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-600/25 transition transform active:scale-95"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Add New Task</span>
                        </button>
                    </div>
                </div>

                <!-- Status Filter Pills for Tasks -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <button
                        v-for="taskStatus in taskStatusFilters"
                        :key="taskStatus"
                        @click="selectedTaskFilter = taskStatus"
                        class="px-3 py-1.5 rounded-xl text-xs font-medium transition"
                        :class="selectedTaskFilter === taskStatus
                            ? 'bg-slate-800 text-white border border-slate-700'
                            : 'text-slate-400 hover:text-slate-200'"
                    >
                        {{ taskStatus }}
                    </button>
                </div>

                <!-- Empty State for Tasks -->
                <div v-if="!filteredTasks.length" class="text-center py-12 px-4 border border-dashed border-slate-800 rounded-xl space-y-3">
                    <div class="w-10 h-10 mx-auto rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-300">No tasks in this view</p>
                    <p class="text-xs text-slate-500 max-w-xs mx-auto">
                        {{ selectedTaskFilter === 'All' ? 'This project has no tasks yet. Add one to get started!' : `No tasks matching the '${selectedTaskFilter}' status.` }}
                    </p>
                    <button
                        v-if="selectedTaskFilter === 'All'"
                        @click="openCreateTaskModal"
                        class="px-3.5 py-1.5 text-xs font-semibold text-indigo-400 hover:text-indigo-300 bg-indigo-500/10 rounded-lg transition"
                    >
                        + Create First Task
                    </button>
                </div>

                <!-- Tasks List -->
                <div v-else class="space-y-3">
                    <div
                        v-for="task in filteredTasks"
                        :key="task.id"
                        class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-950/70 hover:bg-slate-950 border border-slate-800/80 hover:border-slate-700/90 rounded-xl transition gap-3 group"
                        :class="{ 'opacity-60': task.status === 'Completed' }"
                    >
                        <!-- Left Checkbox / Toggle & Details -->
                        <div class="flex items-start gap-3.5 min-w-0 flex-1">
                            <!-- Toggle Complete Button / Checkbox -->
                            <button
                                @click="toggleTaskCompletion(task)"
                                :disabled="togglingTaskId === task.id"
                                title="Toggle Complete / Incomplete"
                                class="mt-0.5 w-5 h-5 rounded-md border flex items-center justify-center shrink-0 transition-all duration-200"
                                :class="task.status === 'Completed'
                                    ? 'bg-cyan-500 border-cyan-500 text-slate-950'
                                    : 'border-slate-700 hover:border-indigo-500 text-transparent bg-slate-900'"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>

                            <!-- Title & Description -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4
                                        class="text-sm font-semibold transition"
                                        :class="task.status === 'Completed' ? 'line-through text-slate-400' : 'text-slate-100'"
                                    >
                                        {{ task.title }}
                                    </h4>
                                    <StatusBadge :status="task.status" />
                                </div>
                                <p v-if="task.description" class="mt-1 text-xs text-slate-400 line-clamp-2">
                                    {{ task.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                            <!-- Quick Status Change Select -->
                            <select
                                :value="task.status"
                                @change="handleQuickStatusChange(task, $event.target.value)"
                                class="px-2.5 py-1 bg-slate-900 border border-slate-800 rounded-lg text-xs text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 cursor-pointer"
                            >
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>

                            <button
                                @click="openEditTaskModal(task)"
                                title="Edit Task"
                                class="p-1.5 text-slate-400 hover:text-indigo-400 hover:bg-slate-800 rounded-lg transition"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button
                                @click="confirmDeleteTask(task)"
                                title="Delete Task"
                                class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Modals -->
        <ProjectModal
            :is-open="isEditProjectOpen"
            :project="project"
            @close="isEditProjectOpen = false"
            @saved="handleProjectSaved"
        />

        <ConfirmModal
            :is-open="isDeleteProjectOpen"
            :title="'Delete Project'"
            :message="`Are you sure you want to delete '${project?.title}'? All associated tasks will be permanently removed.`"
            :is-loading="isDeletingProject"
            @confirm="handleDeleteProjectConfirmed"
            @cancel="isDeleteProjectOpen = false"
        />

        <!-- Task Modals -->
        <TaskModal
            v-if="project"
            :is-open="isTaskModalOpen"
            :project-id="project.id"
            :task="selectedTask"
            @close="isTaskModalOpen = false"
            @saved="handleTaskSaved"
        />

        <ConfirmModal
            :is-open="isDeleteTaskOpen"
            :title="'Delete Task'"
            :message="`Are you sure you want to delete '${taskToDelete?.title}'?`"
            :is-loading="isDeletingTask"
            @confirm="handleDeleteTaskConfirmed"
            @cancel="isDeleteTaskOpen = false"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { apiService } from '../services/api';
import { useToast } from '../services/toast';
import StatusBadge from '../components/StatusBadge.vue';
import ProjectModal from '../components/ProjectModal.vue';
import TaskModal from '../components/TaskModal.vue';
import ConfirmModal from '../components/ConfirmModal.vue';

const route = useRoute();
const router = useRouter();
const { success, error: toastError } = useToast();

const project = ref(null);
const loading = ref(true);
const error = ref('');

// Task filters
const taskStatusFilters = ['All', 'Pending', 'In Progress', 'Completed'];
const selectedTaskFilter = ref('All');
const togglingTaskId = ref(null);

// Project Edit/Delete modals
const isEditProjectOpen = ref(false);
const isDeleteProjectOpen = ref(false);
const isDeletingProject = ref(false);

// Task Add/Edit/Delete modals
const isTaskModalOpen = ref(false);
const selectedTask = ref(null);
const isDeleteTaskOpen = ref(false);
const taskToDelete = ref(null);
const isDeletingTask = ref(false);

const fetchProject = async () => {
    loading.value = true;
    error.value = '';

    try {
        const response = await apiService.getProject(route.params.id);
        project.value = response.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load project details.';
    } finally {
        loading.value = false;
    }
};

const filteredTasks = computed(() => {
    if (!project.value?.tasks) return [];
    if (selectedTaskFilter.value === 'All') return project.value.tasks;
    return project.value.tasks.filter(t => t.status === selectedTaskFilter.value);
});

const pendingCount = computed(() => {
    if (!project.value?.tasks) return 0;
    return project.value.tasks.filter(t => t.status === 'Pending').length;
});

const completionPercentage = computed(() => {
    if (!project.value || !project.value.tasks_count || project.value.tasks_count === 0) return 0;
    return Math.round(((project.value.completed_tasks_count || 0) / project.value.tasks_count) * 100);
});

const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

// Handle project updates/deletion
const handleProjectSaved = (updatedProject) => {
    project.value = { ...project.value, ...updatedProject };
    fetchProject();
};

const handleDeleteProjectConfirmed = async () => {
    if (!project.value) return;
    isDeletingProject.value = true;
    try {
        await apiService.deleteProject(project.value.id);
        success(`Project '${project.value.title}' deleted successfully.`);
        router.push('/projects');
    } catch (err) {
        toastError(err.response?.data?.message || 'Failed to delete project.');
    } finally {
        isDeletingProject.value = false;
        isDeleteProjectOpen.value = false;
    }
};

// Task Handlers
const openCreateTaskModal = () => {
    selectedTask.value = null;
    isTaskModalOpen.value = true;
};

const openEditTaskModal = (task) => {
    selectedTask.value = { ...task };
    isTaskModalOpen.value = true;
};

const handleTaskSaved = () => {
    fetchProject();
};

const toggleTaskCompletion = async (task) => {
    togglingTaskId.value = task.id;
    try {
        await apiService.toggleTask(task.id);
        success(`Task status updated!`);
        await fetchProject();
    } catch (err) {
        toastError(err.response?.data?.message || 'Failed to update task.');
    } finally {
        togglingTaskId.value = null;
    }
};

const handleQuickStatusChange = async (task, newStatus) => {
    try {
        await apiService.updateTask(task.id, { status: newStatus });
        success(`Task status changed to ${newStatus}`);
        await fetchProject();
    } catch (err) {
        toastError(err.response?.data?.message || 'Failed to update task status.');
    }
};

const confirmDeleteTask = (task) => {
    taskToDelete.value = task;
    isDeleteTaskOpen.value = true;
};

const handleDeleteTaskConfirmed = async () => {
    if (!taskToDelete.value) return;
    isDeletingTask.value = true;
    try {
        await apiService.deleteTask(taskToDelete.value.id);
        success(`Task '${taskToDelete.value.title}' deleted.`);
        isDeleteTaskOpen.value = false;
        taskToDelete.value = null;
        await fetchProject();
    } catch (err) {
        toastError(err.response?.data?.message || 'Failed to delete task.');
    } finally {
        isDeletingTask.value = false;
    }
};

onMounted(() => {
    fetchProject();
});
</script>
