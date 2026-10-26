<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Projects <span class="text-indigo-600">Directory</span>
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Track, filter, and manage all client projects and deliverables.
                </p>
            </div>
            <button
                @click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-100 transition transform active:scale-95 self-start sm:self-auto"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Project</span>
            </button>
        </div>

        <!-- Filter & Control Toolbar -->
        <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Status Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
                <button
                    v-for="statusOption in statusOptions"
                    :key="statusOption"
                    @click="setStatusFilter(statusOption)"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition shrink-0"
                    :class="selectedStatus === statusOption
                        ? 'bg-indigo-600 text-white shadow-xs'
                        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                >
                    {{ statusOption }}
                    <span
                        v-if="projectCountByStatus(statusOption) !== null"
                        class="ml-1.5 px-1.5 py-0.5 rounded-full text-[10px]"
                        :class="selectedStatus === statusOption ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-600'"
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
                        class="w-full pl-9 pr-3.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                    <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3 top-2.5 text-xs"></i>
                </div>

                <!-- Sort Order -->
                <select
                    v-model="sortBy"
                    @change="fetchProjects"
                    class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer"
                >
                    <option value="created_at">Newest First</option>
                    <option value="deadline">Upcoming Deadline</option>
                    <option value="title">Alphabetical (A-Z)</option>
                    <option value="status">Status</option>
                </select>

                <!-- View Layout Toggle (Card vs Table) -->
                <div class="flex items-center p-0.5 bg-slate-100 border border-slate-200 rounded-xl">
                    <button
                        @click="viewLayout = 'cards'"
                        title="Card Layout"
                        class="p-1.5 rounded-lg transition text-xs"
                        :class="viewLayout === 'cards' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                    >
                        <i class="fa-solid fa-grip"></i>
                    </button>
                    <button
                        @click="viewLayout = 'table'"
                        title="Table Layout"
                        class="p-1.5 rounded-lg transition text-xs"
                        :class="viewLayout === 'table' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                    >
                        <i class="fa-solid fa-table-list"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State Skeleton -->
        <div v-if="loading && !projects.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div v-for="i in 6" :key="i" class="p-6 bg-white border border-slate-200 rounded-2xl animate-pulse space-y-4 shadow-xs">
                <div class="h-5 w-3/4 bg-slate-100 rounded"></div>
                <div class="h-14 w-full bg-slate-50 rounded"></div>
                <div class="h-4 w-1/2 bg-slate-100 rounded"></div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="p-6 bg-rose-50 border border-rose-200 rounded-2xl text-center space-y-3">
            <p class="text-rose-700 font-semibold">{{ error }}</p>
            <button
                @click="fetchProjects"
                class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl"
            >
                Retry
            </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!projects.length" class="p-12 bg-white border border-slate-200 rounded-2xl text-center space-y-4 shadow-xs">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">No projects found</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    {{ searchQuery || selectedStatus !== 'All' ? 'Try adjusting your filter or search query.' : 'Get started by creating your first project.' }}
                </p>
            </div>
            <button
                v-if="searchQuery || selectedStatus !== 'All'"
                @click="resetFilters"
                class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition"
            >
                Clear Filters
            </button>
            <button
                v-else
                @click="openCreateModal"
                class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition"
            >
                Create Project
            </button>
        </div>

        <!-- CARD VIEW -->
        <div v-else-if="viewLayout === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div
                v-for="project in projects"
                :key="project.id"
                class="group flex flex-col justify-between p-6 bg-white hover:bg-slate-50/50 border border-slate-200 hover:border-slate-300 rounded-2xl shadow-xs hover:shadow-md transition-all duration-200"
            >
                <div>
                    <!-- Top row: Status and actions -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <StatusBadge :status="project.status" />
                        <div class="flex items-center gap-1">
                            <button
                                @click="openEditModal(project)"
                                title="Edit Project Details"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                            >
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <button
                                @click="confirmDelete(project)"
                                title="Delete Project"
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                            >
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Title -->
                    <router-link
                        :to="`/projects/${project.id}`"
                        class="text-lg font-bold text-slate-900 hover:text-indigo-600 transition block line-clamp-1 group-hover:text-indigo-600"
                    >
                        {{ project.title }}
                    </router-link>

                    <!-- Description -->
                    <p class="mt-2 text-xs text-slate-500 line-clamp-2 leading-relaxed">
                        {{ project.description || 'No description provided.' }}
                    </p>
                </div>

                <!-- Footer details -->
                <div class="mt-6 pt-4 border-t border-slate-100 space-y-3">
                    <!-- Progress Bar -->
                    <div>
                        <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500 mb-1.5">
                            <span>Tasks Progress</span>
                            <span class="text-slate-700">
                                {{ project.completed_tasks_count || 0 }} / {{ project.tasks_count || 0 }}
                                ({{ calculatePercentage(project.completed_tasks_count, project.tasks_count) }}%)
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-indigo-500 to-blue-500 h-2 rounded-full transition-all duration-300"
                                :style="{ width: calculatePercentage(project.completed_tasks_count, project.tasks_count) + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Deadline and View Link -->
                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                            <i class="fa-regular fa-calendar-days text-slate-400"></i>
                            <span>{{ project.deadline ? formatDate(project.deadline) : 'No deadline' }}</span>
                        </div>

                        <router-link
                            :to="`/projects/${project.id}`"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition"
                        >
                            <span>View Details</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE VIEW -->
        <div v-else class="overflow-x-auto bg-white border border-slate-200 rounded-2xl shadow-xs">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th scope="col" class="py-3.5 px-4">Title & Details</th>
                        <th scope="col" class="py-3.5 px-4">Status</th>
                        <th scope="col" class="py-3.5 px-4">Task Deliverables</th>
                        <th scope="col" class="py-3.5 px-4">Deadline</th>
                        <th scope="col" class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr
                        v-for="project in projects"
                        :key="project.id"
                        class="hover:bg-slate-50/70 transition group"
                    >
                        <td class="py-4 px-4 min-w-[240px]">
                            <router-link
                                :to="`/projects/${project.id}`"
                                class="font-bold text-slate-900 hover:text-indigo-600 transition"
                            >
                                {{ project.title }}
                            </router-link>
                            <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">
                                {{ project.description || 'No description' }}
                            </p>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <StatusBadge :status="project.status" />
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap min-w-[160px]">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div
                                        class="bg-indigo-600 h-2 rounded-full"
                                        :style="{ width: calculatePercentage(project.completed_tasks_count, project.tasks_count) + '%' }"
                                    ></div>
                                </div>
                                <span class="text-xs text-slate-600 font-mono font-medium">
                                    {{ project.completed_tasks_count }}/{{ project.tasks_count }}
                                </span>
                            </div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                            {{ project.deadline ? formatDate(project.deadline) : 'N/A' }}
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <router-link
                                    :to="`/projects/${project.id}`"
                                    title="View Details"
                                    class="p-2 text-slate-400 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition"
                                >
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </router-link>
                                <button
                                    @click="openEditModal(project)"
                                    title="Edit Project"
                                    class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                >
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button
                                    @click="confirmDelete(project)"
                                    title="Delete Project"
                                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                >
                                    <i class="fa-solid fa-trash-can text-xs"></i>
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
