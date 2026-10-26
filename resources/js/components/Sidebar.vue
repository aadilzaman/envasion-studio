<template>
    <div>
        <!-- Mobile Header Bar (Only visible on small mobile screens to toggle the sidebar) -->
        <div class="lg:hidden flex items-center justify-between p-4 bg-white border-b border-slate-200 shadow-sm">
            <router-link to="/" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow">
                    <i class="fa-solid fa-cubes"></i>
                </div>
                <span class="font-extrabold text-slate-900 tracking-tight text-base">
                    Envision<span class="text-indigo-600">Studio</span>
                </span>
            </router-link>

            <button
                @click="mobileSidebarOpen = !mobileSidebarOpen"
                class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition"
                aria-label="Toggle navigation"
            >
                <i :class="mobileSidebarOpen ? 'fa-solid fa-xmark text-lg' : 'fa-solid fa-bars text-lg'"></i>
            </button>
        </div>

        <!-- Backdrop for mobile drawer -->
        <div
            v-if="mobileSidebarOpen"
            @click="mobileSidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden"
        ></div>

        <!-- Sidebar Container (Fixed on Desktop, Drawer on Mobile) -->
        <aside
            class="fixed top-0 bottom-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="mobileSidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0'"
        >
            <div>
                <!-- Brand / Logo -->
                <div class="h-20 flex items-center px-6 border-b border-slate-100">
                    <router-link to="/" class="flex items-center gap-3 group" @click="mobileSidebarOpen = false">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center shadow-md shadow-indigo-200 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-cubes text-lg"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-lg font-extrabold tracking-tight text-slate-900 group-hover:text-indigo-600 transition-colors">
                                Envision<span class="text-indigo-600">Studio</span>
                            </span>
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 -mt-0.5">
                                Agency Management
                            </span>
                        </div>
                    </router-link>
                </div>

                <!-- Primary Navigation -->
                <div class="px-4 py-6 space-y-1.5">
                    <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Menu
                    </span>

                    <router-link
                        to="/"
                        @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all mt-1"
                        :class="$route.path === '/'
                            ? 'bg-indigo-50 text-indigo-700 shadow-xs'
                            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                    >
                        <i class="fa-solid fa-chart-pie w-5 text-center" :class="$route.path === '/' ? 'text-indigo-600' : 'text-slate-400'"></i>
                        <span>Dashboard</span>
                    </router-link>

                    <router-link
                        to="/projects"
                        @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        :class="$route.path.startsWith('/projects')
                            ? 'bg-indigo-50 text-indigo-700 shadow-xs'
                            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                    >
                        <i class="fa-solid fa-folder-tree w-5 text-center" :class="$route.path.startsWith('/projects') ? 'text-indigo-600' : 'text-slate-400'"></i>
                        <span>Projects</span>
                    </router-link>
                </div>

                <!-- Quick Actions Section -->
                <div class="px-4 py-2 space-y-2.5">
                    <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Actions
                    </span>

                    <button
                        @click="$emit('open-create-project'); mobileSidebarOpen = false"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-100 transition-all transform active:scale-98"
                    >
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>New Project</span>
                    </button>

                    <button
                        @click="handleResetMockData"
                        :disabled="isResetting"
                        class="w-full flex items-center justify-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all disabled:opacity-50"
                        title="Restore initial 10 projects & 20 tasks"
                    >
                        <i class="fa-solid fa-arrows-rotate text-xs" :class="{ 'fa-spin': isResetting }"></i>
                        <span>{{ isResetting ? 'Resetting...' : 'Reset Mock Data' }}</span>
                    </button>
                </div>
            </div>

            <!-- Footer / System Meta in Sidebar -->
            <div class="p-4 m-4 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-600">
                    <span class="font-medium">System Status</span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-600 font-semibold text-[11px]">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live API
                    </span>
                </div>
                <div class="text-[11px] text-slate-400">
                    Laravel 12 + Vue 3 Light UI
                </div>
            </div>
        </aside>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { apiService } from '../services/api';
import { useToast } from '../services/toast';

const emit = defineEmits(['open-create-project', 'data-refreshed']);
const { success, error } = useToast();

const mobileSidebarOpen = ref(false);
const isResetting = ref(false);

const handleResetMockData = async () => {
    if (!confirm('Are you sure you want to restore the database to the original 10 sample projects and 20 tasks?')) {
        return;
    }

    try {
        isResetting.value = true;
        await apiService.resetMockData();
        success('Database successfully reset to original mock dataset!');
        emit('data-refreshed');
        mobileSidebarOpen.value = false;
    } catch (err) {
        error(err.response?.data?.message || 'Failed to reset mock data.');
    } finally {
        isResetting.value = false;
    }
};
</script>
