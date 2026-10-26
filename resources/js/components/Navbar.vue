<template>
    <header class="sticky top-0 z-40 w-full border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-8">
                    <router-link to="/" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition-transform">
                            <span class="text-white font-black text-lg">E</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-base font-bold tracking-tight text-white group-hover:text-indigo-400 transition-colors">
                                Envision<span class="text-indigo-400">Studio</span>
                            </span>
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 -mt-1">
                                Agency Dashboard
                            </span>
                        </div>
                    </router-link>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex items-center gap-1">
                        <router-link
                            to="/"
                            class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all"
                            :class="$route.path === '/' ? 'bg-indigo-600/15 text-indigo-400 border border-indigo-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900'"
                        >
                            Dashboard
                        </router-link>
                        <router-link
                            to="/projects"
                            class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all"
                            :class="$route.path.startsWith('/projects') ? 'bg-indigo-600/15 text-indigo-400 border border-indigo-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900'"
                        >
                            Projects
                        </router-link>
                    </nav>
                </div>

                <!-- Right Side Actions -->
                <div class="hidden sm:flex items-center gap-3">
                    <!-- Reset Mock Data Button -->
                    <button
                        @click="handleResetMockData"
                        :disabled="isResetting"
                        title="Reset database to original 10 projects & 20 tasks"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-400 hover:text-slate-200 bg-slate-900 hover:bg-slate-800 border border-slate-700/60 rounded-lg transition-all disabled:opacity-50"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" :class="{ 'animate-spin': isResetting }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>{{ isResetting ? 'Resetting...' : 'Reset Mock Data' }}</span>
                    </button>

                    <!-- Add Project Button -->
                    <button
                        @click="$emit('open-create-project')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 rounded-lg shadow-md shadow-indigo-600/25 transition-all transform active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>New Project</span>
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center sm:hidden">
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="p-2 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-slate-900 focus:outline-none"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div v-if="mobileMenuOpen" class="sm:hidden border-b border-slate-800 bg-slate-950 px-4 pt-2 pb-4 space-y-2">
            <router-link
                to="/"
                @click="mobileMenuOpen = false"
                class="block px-3 py-2 rounded-md text-base font-medium"
                :class="$route.path === '/' ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-900'"
            >
                Dashboard
            </router-link>
            <router-link
                to="/projects"
                @click="mobileMenuOpen = false"
                class="block px-3 py-2 rounded-md text-base font-medium"
                :class="$route.path.startsWith('/projects') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:bg-slate-900'"
            >
                Projects
            </router-link>
            <div class="pt-2 border-t border-slate-800 flex flex-col gap-2">
                <button
                    @click="$emit('open-create-project'); mobileMenuOpen = false"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg"
                >
                    + New Project
                </button>
                <button
                    @click="handleResetMockData; mobileMenuOpen = false"
                    class="w-full py-2 text-xs text-slate-400 bg-slate-900 rounded-lg hover:text-slate-200"
                >
                    Reset Mock Data
                </button>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref } from 'vue';
import { apiService } from '../services/api';
import { useToast } from '../services/toast';

const emit = defineEmits(['open-create-project', 'data-refreshed']);
const { success, error } = useToast();

const mobileMenuOpen = ref(false);
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
    } catch (err) {
        error(err.response?.data?.message || 'Failed to reset mock data.');
    } finally {
        isResetting.value = false;
    }
};
</script>
