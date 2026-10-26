<template>
    <div class="space-y-8">
        <!-- Hero Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    Agency <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400">Dashboard</span>
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Real-time operational overview across all agency client initiatives and task deliverables.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="fetchSummary"
                    :disabled="loading"
                    class="p-2.5 text-slate-400 hover:text-slate-200 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded-xl transition"
                    title="Refresh data"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <router-link
                    to="/projects"
                    class="px-4 py-2.5 text-sm font-semibold text-slate-200 bg-slate-900 hover:bg-slate-800 border border-slate-700/80 rounded-xl transition shadow-sm"
                >
                    View All Projects
                </router-link>
                <button
                    @click="$emit('open-create-project')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 transition transform active:scale-95"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>New Project</span>
                </button>
            </div>
        </div>

        <!-- Loading State Skeleton -->
        <div v-if="loading && !summary" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div v-for="i in 4" :key="i" class="p-6 bg-slate-900/60 border border-slate-800 rounded-2xl animate-pulse space-y-4">
                <div class="w-10 h-10 rounded-xl bg-slate-800"></div>
                <div class="h-4 w-24 bg-slate-800 rounded"></div>
                <div class="h-8 w-16 bg-slate-800 rounded"></div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="p-6 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-center space-y-3">
            <p class="text-rose-400 font-medium">{{ error }}</p>
            <button
                @click="fetchSummary"
                class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-xl"
            >
                Retry
            </button>
        </div>

        <!-- Content -->
        <div v-else-if="summary" class="space-y-8">
            <!-- 4 Required Core Summary Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Total Projects -->
                <div class="relative overflow-hidden p-6 bg-slate-900/80 border border-slate-800/80 rounded-2xl hover:border-indigo-500/40 transition shadow-xl group">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl group-hover:bg-indigo-500/10 transition"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Projects</span>
                        <div class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-white">{{ summary.total_projects }}</span>
                        <span class="text-xs text-slate-500">managed</span>
                    </div>
                    <div class="mt-3 flex items-center gap-2 text-xs text-slate-400">
                        <span class="text-emerald-400 font-medium">{{ summary.completed_projects }} completed</span>
                        <span>•</span>
                        <span class="text-amber-400 font-medium">{{ summary.on_hold_projects }} on hold</span>
                    </div>
                </div>

                <!-- Active Projects -->
                <div class="relative overflow-hidden p-6 bg-slate-900/80 border border-slate-800/80 rounded-2xl hover:border-emerald-500/40 transition shadow-xl group">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Projects</span>
                        <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-emerald-400">{{ summary.active_projects }}</span>
                        <span class="text-xs text-slate-500">in execution</span>
                    </div>
                    <div class="mt-3 text-xs text-slate-400">
                        <span>{{ Math.round((summary.active_projects / (summary.total_projects || 1)) * 100) }}% of total portfolio</span>
                    </div>
                </div>

                <!-- Completed Tasks -->
                <div class="relative overflow-hidden p-6 bg-slate-900/80 border border-slate-800/80 rounded-2xl hover:border-cyan-500/40 transition shadow-xl group">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-cyan-500/5 rounded-full blur-xl group-hover:bg-cyan-500/10 transition"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Completed Tasks</span>
                        <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-cyan-400">{{ summary.completed_tasks }}</span>
                        <span class="text-xs text-slate-500">done</span>
                    </div>
                    <div class="mt-3 flex items-center gap-1.5 text-xs text-slate-400">
                        <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-cyan-500 to-indigo-500 h-1.5 rounded-full" :style="{ width: summary.task_completion_rate + '%' }"></div>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-300">{{ summary.task_completion_rate }}%</span>
                    </div>
                </div>

                <!-- Pending Tasks -->
                <div class="relative overflow-hidden p-6 bg-slate-900/80 border border-slate-800/80 rounded-2xl hover:border-amber-500/40 transition shadow-xl group">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-500/5 rounded-full blur-xl group-hover:bg-amber-500/10 transition"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Tasks</span>
                        <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-amber-400">{{ summary.pending_tasks }}</span>
                        <span class="text-xs text-slate-500">queued</span>
                    </div>
                    <div class="mt-3 text-xs text-slate-400">
                        <span>{{ summary.in_progress_tasks }} tasks currently in progress</span>
                    </div>
                </div>
            </div>

            <!-- Two Column Section: Recent Projects & Upcoming Deadlines -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Projects List (2 columns on lg) -->
                <div class="lg:col-span-2 p-6 bg-slate-900/80 border border-slate-800/80 rounded-2xl shadow-xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div>
                            <h2 class="text-base font-bold text-slate-100">Recent Agency Projects</h2>
                            <p class="text-xs text-slate-400">Latest active and scheduled initiatives</p>
                        </div>
                        <router-link
                            to="/projects"
                            class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition"
                        >
                            Explore all &rarr;
                        </router-link>
                    </div>

                    <div v-if="!summary.recent_projects?.length" class="text-center py-8 text-sm text-slate-500">
                        No projects created yet.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="project in summary.recent_projects"
                            :key="project.id"
                            class="flex items-center justify-between p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/60 hover:border-slate-700 transition group"
                        >
                            <div class="min-w-0 pr-4">
                                <router-link
                                    :to="`/projects/${project.id}`"
                                    class="text-sm font-semibold text-slate-200 group-hover:text-indigo-400 transition truncate block"
                                >
                                    {{ project.title }}
                                </router-link>
                                <p class="text-xs text-slate-400 line-clamp-1 mt-0.5">
                                    {{ project.description || 'No description provided' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <StatusBadge :status="project.status" />
                                <span class="text-xs text-slate-500 hidden sm:inline">
                                    {{ project.completed_tasks_count }} / {{ project.tasks_count }} tasks
                                </span>
                                <router-link
                                    :to="`/projects/${project.id}`"
                                    class="p-1 text-slate-400 group-hover:text-indigo-400 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Deadlines Sidebar -->
                <div class="p-6 bg-slate-900/80 border border-slate-800/80 rounded-2xl shadow-xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div>
                            <h2 class="text-base font-bold text-slate-100">Upcoming Deadlines</h2>
                            <p class="text-xs text-slate-400">Milestone dates to keep on radar</p>
                        </div>
                        <div class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <div v-if="!summary.upcoming_deadlines?.length" class="text-center py-8 text-sm text-slate-500">
                        No upcoming deadlines scheduled.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="proj in summary.upcoming_deadlines"
                            :key="proj.id"
                            class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/60 space-y-1.5"
                        >
                            <div class="flex items-center justify-between">
                                <router-link
                                    :to="`/projects/${proj.id}`"
                                    class="text-xs font-semibold text-slate-200 hover:text-indigo-400 transition truncate block max-w-[160px]"
                                >
                                    {{ proj.title }}
                                </router-link>
                                <span class="text-[11px] font-mono font-medium px-2 py-0.5 rounded bg-slate-800 text-indigo-300">
                                    {{ formatDate(proj.deadline) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-400">
                                <StatusBadge :status="proj.status" />
                                <span>{{ daysRemainingText(proj.deadline) }}</span>
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
