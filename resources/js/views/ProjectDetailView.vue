<template>
    <div class="space-y-6">
        <!-- Breadcrumbs Navigation -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <router-link to="/" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                <i class="fa-solid fa-chart-pie text-[11px]"></i>
                <span>Dashboard</span>
            </router-link>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
            <router-link to="/projects" class="hover:text-indigo-600 transition">Projects</router-link>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
            <span class="text-indigo-600 font-bold truncate max-w-xs">{{ project?.title || 'Details' }}</span>
        </nav>

        <!-- Loading Skeleton -->
        <div v-if="loading && !project" class="space-y-6 animate-pulse">
            <div class="p-8 bg-white border border-slate-200 rounded-2xl space-y-4 shadow-xs">
                <div class="h-8 w-1/2 bg-slate-100 rounded"></div>
                <div class="h-4 w-3/4 bg-slate-50 rounded"></div>
                <div class="h-6 w-32 bg-slate-100 rounded"></div>
            </div>
            <div class="p-6 bg-white border border-slate-200 rounded-2xl space-y-4 shadow-xs">
                <div class="h-6 w-40 bg-slate-100 rounded"></div>
                <div class="h-12 w-full bg-slate-50 rounded"></div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="p-8 bg-rose-50 border border-rose-200 rounded-2xl text-center space-y-4">
            <p class="text-rose-700 font-bold text-base">{{ error }}</p>
            <div class="flex items-center justify-center gap-3">
                <button
                    @click="fetchProject"
                    class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl"
                >
                    Retry
                </button>
                <router-link
                    to="/projects"
                    class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl"
                >
                    Back to Projects
                </router-link>
            </div>
        </div>

        <!-- Project Loaded Content -->
        <div v-else-if="project" class="space-y-6">
            <!-- Project Overview Banner Card -->
            <div class="p-6 sm:p-8 bg-white border border-slate-200 rounded-2xl shadow-xs space-y-6">
                <!-- Header row -->
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                {{ project.title }}
                            </h1>
                            <StatusBadge :status="project.status" />
                        </div>
                        <p class="text-sm text-slate-600 max-w-3xl leading-relaxed whitespace-pre-line">
                            {{ project.description || 'No detailed project description has been specified.' }}
                        </p>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                        <button
                            @click="isEditProjectOpen = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition"
                        >
                            <i class="fa-solid fa-pen-to-square text-xs text-slate-500"></i>
                            <span>Edit Project</span>
                        </button>
                        <button
                            @click="isDeleteProjectOpen = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition"
                        >
                            <i class="fa-solid fa-trash-can text-xs text-rose-500"></i>
                            <span>Delete</span>
                        </button>
                    </div>
                </div>

                <!-- Metrics and Deadline strip -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100">
                    <div class="p-3.5 bg-slate-50/80 border border-slate-200/80 rounded-xl">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Tasks</span>
                        <p class="text-xl font-extrabold text-slate-900 mt-0.5">{{ project.tasks_count || 0 }}</p>
                    </div>
                    <div class="p-3.5 bg-blue-50/60 border border-blue-100 rounded-xl">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Completed</span>
                        <p class="text-xl font-extrabold text-blue-700 mt-0.5">{{ project.completed_tasks_count || 0 }}</p>
                    </div>
                    <div class="p-3.5 bg-amber-50/60 border border-amber-100 rounded-xl">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Pending</span>
                        <p class="text-xl font-extrabold text-amber-700 mt-0.5">{{ pendingCount }}</p>
                    </div>
                    <div class="p-3.5 bg-slate-50/80 border border-slate-200/80 rounded-xl">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Target Deadline</span>
                        <p class="text-sm font-semibold text-slate-800 mt-1">
                            {{ project.deadline ? formatDate(project.deadline) : 'None' }}
                        </p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div>
                    <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-2">
                        <span>Project Completion Progress</span>
                        <span class="text-indigo-600 font-mono">{{ completionPercentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden p-0.5 border border-slate-200">
                        <div
                            class="bg-gradient-to-r from-indigo-500 to-blue-500 h-1.5 rounded-full transition-all duration-500"
                            :style="{ width: completionPercentage + '%' }"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- TASK MANAGEMENT SECTION -->
            <div class="p-6 sm:p-8 bg-white border border-slate-200 rounded-2xl shadow-xs space-y-6">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                            <span>Project Deliverables & Tasks</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                {{ filteredTasks.length }}
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Manage, track, and complete milestones for this client deliverable.
                        </p>
                    </div>

                    <!-- Right Controls -->
                    <div class="flex items-center gap-3">
                        <button
                            @click="openCreateTaskModal"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-100 transition transform active:scale-95"
                        >
                            <i class="fa-solid fa-plus text-xs"></i>
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
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition"
                        :class="selectedTaskFilter === taskStatus
                            ? 'bg-slate-900 text-white'
                            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                    >
                        {{ taskStatus }}
                    </button>
                </div>

                <!-- Empty State for Tasks -->
                <div v-if="!filteredTasks.length" class="text-center py-12 px-4 border border-dashed border-slate-200 rounded-2xl space-y-3">
                    <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-700">No tasks in this view</p>
                    <p class="text-xs text-slate-400 max-w-xs mx-auto">
                        {{ selectedTaskFilter === 'All' ? 'This project has no tasks yet. Add one to get started!' : `No tasks matching the '${selectedTaskFilter}' status.` }}
                    </p>
                    <button
                        v-if="selectedTaskFilter === 'All'"
                        @click="openCreateTaskModal"
                        class="px-3.5 py-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 border border-indigo-200 rounded-lg transition"
                    >
                        + Create First Task
                    </button>
                </div>

                <!-- Tasks List -->
                <div v-else class="space-y-3">
                    <div
                        v-for="task in filteredTasks"
                        :key="task.id"
                        class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50/60 hover:bg-slate-50 border border-slate-200/80 hover:border-slate-300 rounded-xl transition gap-3 group"
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
                                    ? 'bg-blue-600 border-blue-600 text-white'
                                    : 'border-slate-300 hover:border-indigo-500 text-transparent bg-white'"
                            >
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </button>

                            <!-- Title & Description -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4
                                        class="text-sm font-semibold transition"
                                        :class="task.status === 'Completed' ? 'line-through text-slate-400' : 'text-slate-800'"
                                    >
                                        {{ task.title }}
                                    </h4>
                                    <StatusBadge :status="task.status" />
                                </div>
                                <p v-if="task.description" class="mt-1 text-xs text-slate-500 line-clamp-2">
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
                                class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 cursor-pointer"
                            >
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>

                            <button
                                @click="openEditTaskModal(task)"
                                title="Edit Task"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-white rounded-lg transition"
                            >
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <button
                                @click="confirmDeleteTask(task)"
                                title="Delete Task"
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-white rounded-lg transition"
                            >
                                <i class="fa-solid fa-trash-can text-xs"></i>
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
