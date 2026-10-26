<template>
    <div class="space-y-8">
        <!-- Hero Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Agency <span class="text-indigo-600">Dashboard</span>
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Real-time operational overview across all agency client initiatives and task deliverables.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="fetchSummary"
                    :disabled="loading"
                    class="p-2.5 text-slate-500 hover:text-slate-800 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition"
                    title="Refresh data"
                >
                    <i class="fa-solid fa-arrows-rotate text-sm" :class="{ 'fa-spin': loading }"></i>
                </button>
                <router-link
                    to="/projects"
                    class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition"
                >
                    View All Projects
                </router-link>
                <button
                    @click="$emit('open-create-project')"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-100 transition transform active:scale-95"
                >
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>New Project</span>
                </button>
            </div>
        </div>

        <!-- Loading State Skeleton -->
        <div v-if="loading && !summary" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div v-for="i in 4" :key="i" class="p-6 bg-white border border-slate-200 rounded-2xl animate-pulse space-y-4 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-slate-100"></div>
                <div class="h-4 w-24 bg-slate-100 rounded"></div>
                <div class="h-8 w-16 bg-slate-100 rounded"></div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="p-6 bg-rose-50 border border-rose-200 rounded-2xl text-center space-y-3">
            <p class="text-rose-700 font-semibold">{{ error }}</p>
            <button
                @click="fetchSummary"
                class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs"
            >
                Retry
            </button>
        </div>

        <!-- Content -->
        <div v-else-if="summary" class="space-y-8">
            <!-- 4 Required Core Summary Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Total Projects -->
                <div class="p-6 bg-white border border-slate-200/80 rounded-2xl hover:border-indigo-300 transition-all shadow-xs hover:shadow-md group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Projects</span>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center">
                            <i class="fa-solid fa-folder-closed"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-slate-900">{{ summary.total_projects }}</span>
                        <span class="text-xs text-slate-500">managed</span>
                    </div>
                    <div class="mt-3 flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <span class="text-emerald-600">{{ summary.completed_projects }} completed</span>
                        <span>•</span>
                        <span class="text-amber-600">{{ summary.on_hold_projects }} on hold</span>
                    </div>
                </div>

                <!-- Active Projects -->
                <div class="p-6 bg-white border border-slate-200/80 rounded-2xl hover:border-emerald-300 transition-all shadow-xs hover:shadow-md group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Projects</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-emerald-600">{{ summary.active_projects }}</span>
                        <span class="text-xs text-slate-500">in execution</span>
                    </div>
                    <div class="mt-3 text-xs text-slate-500 font-medium">
                        <span>{{ Math.round((summary.active_projects / (summary.total_projects || 1)) * 100) }}% of total portfolio</span>
                    </div>
                </div>

                <!-- Completed Tasks -->
                <div class="p-6 bg-white border border-slate-200/80 rounded-2xl hover:border-blue-300 transition-all shadow-xs hover:shadow-md group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Completed Tasks</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-blue-600">{{ summary.completed_tasks }}</span>
                        <span class="text-xs text-slate-500">done</span>
                    </div>
                    <div class="mt-3 flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full" :style="{ width: summary.task_completion_rate + '%' }"></div>
                        </div>
                        <span class="text-[11px] font-bold text-slate-700">{{ summary.task_completion_rate }}%</span>
                    </div>
                </div>

                <!-- Pending Tasks -->
                <div class="p-6 bg-white border border-slate-200/80 rounded-2xl hover:border-amber-300 transition-all shadow-xs hover:shadow-md group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pending Tasks</span>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-amber-600">{{ summary.pending_tasks }}</span>
                        <span class="text-xs text-slate-500">queued</span>
                    </div>
                    <div class="mt-3 text-xs text-slate-500 font-medium">
                        <span>{{ summary.in_progress_tasks }} tasks currently in progress</span>
                    </div>
                </div>
            </div>

            <!-- Two Column Section: Recent Projects & Upcoming Deadlines -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Projects List -->
                <div class="lg:col-span-2 p-6 bg-white border border-slate-200 rounded-2xl shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Recent Agency Projects</h2>
                            <p class="text-xs text-slate-500">Latest active and scheduled initiatives</p>
                        </div>
                        <router-link
                            to="/projects"
                            class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1.5"
                        >
                            <span>Explore all</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </router-link>
                    </div>

                    <div v-if="!summary.recent_projects?.length" class="text-center py-8 text-sm text-slate-400">
                        No projects created yet.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="project in summary.recent_projects"
                            :key="project.id"
                            class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50/70 border border-slate-200/80 hover:border-slate-300 hover:bg-slate-50 transition group"
                        >
                            <div class="min-w-0 pr-4">
                                <router-link
                                    :to="`/projects/${project.id}`"
                                    class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition truncate block"
                                >
                                    {{ project.title }}
                                </router-link>
                                <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">
                                    {{ project.description || 'No description provided' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <StatusBadge :status="project.status" />
                                <span class="text-xs text-slate-500 hidden sm:inline font-medium">
                                    {{ project.completed_tasks_count }} / {{ project.tasks_count }} tasks
                                </span>
                                <router-link
                                    :to="`/projects/${project.id}`"
                                    class="p-1 text-slate-400 group-hover:text-indigo-600 transition"
                                >
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Deadlines Sidebar -->
                <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Upcoming Deadlines</h2>
                            <p class="text-xs text-slate-500">Milestones to keep on radar</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                            <i class="fa-regular fa-calendar-days"></i>
                        </div>
                    </div>

                    <div v-if="!summary.upcoming_deadlines?.length" class="text-center py-8 text-sm text-slate-400">
                        No upcoming deadlines scheduled.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="proj in summary.upcoming_deadlines"
                            :key="proj.id"
                            class="p-3 rounded-xl bg-slate-50/70 border border-slate-200/80 space-y-1.5"
                        >
                            <div class="flex items-center justify-between">
                                <router-link
                                    :to="`/projects/${proj.id}`"
                                    class="text-xs font-bold text-slate-900 hover:text-indigo-600 transition truncate block max-w-[160px]"
                                >
                                    {{ proj.title }}
                                </router-link>
                                <span class="text-[11px] font-mono font-semibold px-2 py-0.5 rounded bg-slate-200/70 text-slate-700">
                                    {{ formatDate(proj.deadline) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500">
                                <StatusBadge :status="proj.status" />
                                <span class="font-medium">{{ daysRemainingText(proj.deadline) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { apiService } from '../services/api';
import StatusBadge from '../components/StatusBadge.vue';

defineEmits(['open-create-project']);

const summary = ref(null);
const loading = ref(true);
const error = ref('');

const fetchSummary = async () => {
    loading.value = true;
    error.value = '';
    try {
        const response = await apiService.getDashboardSummary();
        summary.value = response.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load dashboard summary.';
    } finally {
        loading.value = false;
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const daysRemainingText = (deadlineStr) => {
    if (!deadlineStr) return '';
    const deadline = new Date(deadlineStr);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const diffTime = deadline - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 0) return `${Math.abs(diffDays)}d overdue`;
    if (diffDays === 0) return 'Due today';
    return `${diffDays} days left`;
};

onMounted(() => {
    fetchSummary();
});

defineExpose({
    fetchSummary,
});
</script>
