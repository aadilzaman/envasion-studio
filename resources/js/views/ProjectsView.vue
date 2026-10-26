<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Projects <span class="text-indigo-400">Directory</span>
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Track, filter, and manage all client projects and deliverables.
                </p>
            </div>
            <button
                @click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 transition transform active:scale-95 self-start sm:self-auto"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Project</span>
            </button>
        </div>

        <!-- Filter & Control Toolbar -->
        <div class="p-4 bg-slate-900/80 border border-slate-800 rounded-2xl shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Status Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
                <button
                    v-for="statusOption in statusOptions"
                    :key="statusOption"
                    @click="setStatusFilter(statusOption)"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition shrink-0"
                    :class="selectedStatus === statusOption
                        ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25'
                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800'"
                >
                    {{ statusOption }}
                    <span
                        v-if="projectCountByStatus(statusOption) !== null"
                        class="ml-1.5 px-1.5 py-0.5 rounded-full text-[10px]"
                        :class="selectedStatus === statusOption ? 'bg-indigo-700 text-white' : 'bg-slate-800 text-slate-400'"
                    >
                        {{ projectCountByStatus(statusOption) }}
                    </span>
                </button>
            </div>

            <!-- Search, Sort & Layout Switcher -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex-1 sm:w-64">
                    <input
                        v-model="searchQuery"
                        @input="handleSearch"
                        type="text"
                        placeholder="Search projects..."
                        class="w-full pl-9 pr-3.5 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Sort Order -->
                <select
                    v-model="sortBy"
                    @change="fetchProjects"
                    class="px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option value="created_at">Newest First</option>
                    <option value="deadline">Upcoming Deadline</option>
                    <option value="title">Alphabetical (A-Z)</option>
                    <option value="status">Status</option>
                </select>

                <!-- View Layout Toggle (Card vs Table) -->
                <div class="flex items-center p-0.5 bg-slate-950 border border-slate-800 rounded-xl">
                    <button
                        @click="viewLayout = 'cards'"
                        title="Grid / Card Layout"
                        class="p-1.5 rounded-lg transition"
                        :class="viewLayout === 'cards' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-slate-200'"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button
                        @click="viewLayout = 'table'"
                        title="Table Layout"
                        class="p-1.5 rounded-lg transition"
                        :class="viewLayout === 'table' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-slate-200'"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State Skeleton -->
        <div v-if="loading && !projects.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div v-for="i in 6" :key="i" class="p-6 bg-slate-900/60 border border-slate-800 rounded-2xl animate-pulse space-y-4">
                <div class="h-5 w-3/4 bg-slate-800 rounded"></div>
                <div class="h-14 w-full bg-slate-800/60 rounded"></div>
                <div class="h-4 w-1/2 bg-slate-800 rounded"></div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="p-6 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-center space-y-3">
            <p class="text-rose-400 font-medium">{{ error }}</p>
            <button
                @click="fetchProjects"
                class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-xl"
            >
                Retry
            </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!projects.length" class="p-12 bg-slate-900/40 border border-slate-800/80 rounded-2xl text-center space-y-4">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-200">No projects found</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    {{ searchQuery || selectedStatus !== 'All' ? 'Try adjusting your filter or search query.' : 'Get started by creating your first project.' }}
                </p>
            </div>
            <button
                v-if="searchQuery || selectedStatus !== 'All'"
                @click="resetFilters"
                class="px-4 py-2 text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 rounded-xl transition"
            >
                Clear Filters
            </button>
            <button
                v-else
                @click="openCreateModal"
                class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition"
            >
                Create Project
            </button>
        </div>

        <!-- CARD VIEW -->
        <div v-else-if="viewLayout === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div
                v-for="project in projects"
                :key="project.id"
                class="group flex flex-col justify-between p-6 bg-slate-900/80 hover:bg-slate-900 border border-slate-800/80 hover:border-slate-700 rounded-2xl shadow-xl transition-all duration-200"
            >
                <div>
                    <!-- Top row: Status and actions -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <StatusBadge :status="project.status" />
                        <div class="flex items-center gap-1">
                            <button
                                @click="openEditModal(project)"
                                title="Edit Project Details"
                                class="p-1.5 text-slate-400 hover:text-indigo-400 hover:bg-slate-800 rounded-lg transition"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button
                                @click="confirmDelete(project)"
                                title="Delete Project"
                                class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Title -->
                    <router-link
                        :to="`/projects/${project.id}`"
                        class="text-lg font-bold text-slate-100 hover:text-indigo-400 transition block line-clamp-1 group-hover:text-indigo-300"
                    >
                        {{ project.title }}
                    </router-link>

                    <!-- Description -->
                    <p class="mt-2 text-xs text-slate-400 line-clamp-2 leading-relaxed">
                        {{ project.description || 'No description provided.' }}
                    </p>
                </div>

                <!-- Footer details -->
                <div class="mt-6 pt-4 border-t border-slate-800/80 space-y-3">
                    <!-- Progress Bar -->
                    <div>
                        <div class="flex items-center justify-between text-[11px] font-medium text-slate-400 mb-1.5">
                            <span>Tasks Progress</span>
                            <span class="text-slate-300">
                                {{ project.completed_tasks_count || 0 }} / {{ project.tasks_count || 0 }}
                                ({{ calculatePercentage(project.completed_tasks_count, project.tasks_count) }}%)
                            </span>
                        </div>
                        <div class="w-full bg-slate-950 rounded-full h-1.5 overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-indigo-500 to-cyan-500 h-1.5 rounded-full transition-all duration-300"
                                :style="{ width: calculatePercentage(project.completed_tasks_count, project.tasks_count) + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Deadline and View Link -->
                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center gap-1.5 text-xs text-slate-400">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ project.deadline ? formatDate(project.deadline) : 'No deadline' }}</span>
                        </div>

                        <router-link
                            :to="`/projects/${project.id}`"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition"
                        >
                            <span>View Details</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE VIEW -->
        <div v-else class="overflow-x-auto bg-slate-900/80 border border-slate-800 rounded-2xl shadow-xl">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/70 border-b border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th scope="col" class="py-3.5 px-4">Title & Details</th>
                        <th scope="col" class="py-3.5 px-4">Status</th>
                        <th scope="col" class="py-3.5 px-4">Task Deliverables</th>
                        <th scope="col" class="py-3.5 px-4">Deadline</th>
                        <th scope="col" class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <tr
                        v-for="project in projects"
                        :key="project.id"
                        class="hover:bg-slate-800/40 transition group"
                    >
                        <td class="py-4 px-4 min-w-[240px]">
                            <router-link
                                :to="`/projects/${project.id}`"
                                class="font-bold text-slate-100 hover:text-indigo-400 transition"
                            >
                                {{ project.title }}
                            </router-link>
                            <p class="text-xs text-slate-400 line-clamp-1 mt-0.5">
                                {{ project.description || 'No description' }}
                            </p>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <StatusBadge :status="project.status" />
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap min-w-[160px]">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-950 rounded-full h-1.5 overflow-hidden">
                                    <div
                                        class="bg-indigo-500 h-1.5 rounded-full"
                                        :style="{ width: calculatePercentage(project.completed_tasks_count, project.tasks_count) + '%' }"
                                    ></div>
                                </div>
                                <span class="text-xs text-slate-400 font-mono">
                                    {{ project.completed_tasks_count }}/{{ project.tasks_count }}
                                </span>
                            </div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap text-xs text-slate-400 font-mono">
                            {{ project.deadline ? formatDate(project.deadline) : 'N/A' }}
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <router-link
                                    :to="`/projects/${project.id}`"
                                    title="View Details"
                                    class="p-1.5 text-slate-400 hover:text-slate-200 hover:bg-slate-800 rounded-lg transition"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </router-link>
                                <button
                                    @click="openEditModal(project)"
                                    title="Edit Project"
                                    class="p-1.5 text-slate-400 hover:text-indigo-400 hover:bg-slate-800 rounded-lg transition"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button
                                    @click="confirmDelete(project)"
                                    title="Delete Project"
                                    class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modals -->
        <ProjectModal
            :is-open="isProjectModalOpen"
            :project="selectedProject"
            @close="isProjectModalOpen = false"
            @saved="handleProjectSaved"
        />

        <ConfirmModal
            :is-open="isDeleteModalOpen"
            :title="'Delete Project'"
            :message="`Are you sure you want to delete '${projectToDelete?.title}'? All associated tasks will also be deleted.`"
            :is-loading="isDeleting"
            @confirm="handleDeleteConfirmed"
            @cancel="isDeleteModalOpen = false"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { apiService } from '../services/api';
import { useToast } from '../services/toast';
import StatusBadge from '../components/StatusBadge.vue';
import ProjectModal from '../components/ProjectModal.vue';
import ConfirmModal from '../components/ConfirmModal.vue';

const { success, error: toastError } = useToast();

const projects = ref([]);
const allProjectsForCounts = ref([]);
const loading = ref(true);
const error = ref('');

const statusOptions = ['All', 'Active', 'Completed', 'On Hold'];
const selectedStatus = ref('All');
const searchQuery = ref('');
const sortBy = ref('created_at');
const viewLayout = ref('cards'); // 'cards' | 'table'

// Modal states
const isProjectModalOpen = ref(false);
const selectedProject = ref(null);

const isDeleteModalOpen = ref(false);
const projectToDelete = ref(null);
const isDeleting = ref(false);

let searchTimeout = null;

const fetchProjects = async () => {
    loading.value = true;
    error.value = '';

    try {
        const params = {
            status: selectedStatus.value,
            search: searchQuery.value,
            sort_by: sortBy.value,
        };

        const response = await apiService.getProjects(params);
        projects.value = response.data;

        // Fetch count reference if not loaded or when on 'All'
        if (selectedStatus.value === 'All' && !searchQuery.value) {
            allProjectsForCounts.value = response.data;
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch projects.';
    } finally {
        loading.value = false;
    }
};

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchProjects();
    }, 300);
};

const setStatusFilter = (status) => {
    selectedStatus.value = status;
    fetchProjects();
};

const resetFilters = () => {
    selectedStatus.value = 'All';
    searchQuery.value = '';
    sortBy.value = 'created_at';
    fetchProjects();
};

const projectCountByStatus = (status) => {
    if (!allProjectsForCounts.value.length) return null;
    if (status === 'All') return allProjectsForCounts.value.length;
    return allProjectsForCounts.value.filter(p => p.status === status).length;
};

const calculatePercentage = (completed, total) => {
    if (!total || total === 0) return 0;
    return Math.round((completed / total) * 100);
};

const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

// Create / Edit modal triggers
const openCreateModal = () => {
    selectedProject.value = null;
    isProjectModalOpen.value = true;
};

const openEditModal = (project) => {
    selectedProject.value = { ...project };
    isProjectModalOpen.value = true;
};

const handleProjectSaved = () => {
    fetchProjects();
};

// Delete actions
const confirmDelete = (project) => {
    projectToDelete.value = project;
    isDeleteModalOpen.value = true;
};

const handleDeleteConfirmed = async () => {
    if (!projectToDelete.value) return;

    isDeleting.value = true;
    try {
        await apiService.deleteProject(projectToDelete.value.id);
        success(`Project '${projectToDelete.value.title}' deleted successfully.`);
        isDeleteModalOpen.value = false;
        projectToDelete.value = null;
        fetchProjects();
    } catch (err) {
        toastError(err.response?.data?.message || 'Failed to delete project.');
    } finally {
        isDeleting.value = false;
    }
};

onMounted(() => {
    fetchProjects();
});

defineExpose({
    fetchProjects,
    openCreateModal,
});
</script>
