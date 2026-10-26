<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-indigo-500 selection:text-white">
        <!-- Main Navbar -->
        <Navbar
            @open-create-project="isCreateModalOpen = true"
            @data-refreshed="handleGlobalDataRefresh"
        />

        <!-- Main Content Area -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <router-view
                v-slot="{ Component }"
                @open-create-project="isCreateModalOpen = true"
            >
                <transition
                    name="fade"
                    mode="out-in"
                >
                    <component :is="Component" ref="currentViewComponent" />
                </transition>
            </router-view>
        </main>

        <!-- Global Project Modal for "+ New Project" Navbar action -->
        <ProjectModal
            :is-open="isCreateModalOpen"
            :project="null"
            @close="isCreateModalOpen = false"
            @saved="handleGlobalProjectSaved"
        />

        <!-- Toast Notifications -->
        <ToastContainer />

        <!-- Agency Footer -->
        <footer class="border-t border-slate-900 bg-slate-950/60 py-6 text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>EnvisionStudio • Full-Stack Web Developer Practical Assessment</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[11px] font-mono text-slate-400">
                        Laravel 12 + Vue 3 + Tailwind CSS
                    </span>
                    <a
                        href="/api/projects"
                        target="_blank"
                        class="text-indigo-400 hover:text-indigo-300 transition"
                    >
                        REST API
                    </a>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Navbar from './components/Navbar.vue';
import ProjectModal from './components/ProjectModal.vue';
import ToastContainer from './components/ToastContainer.vue';

const router = useRouter();
const isCreateModalOpen = ref(false);
const currentViewComponent = ref(null);

const handleGlobalProjectSaved = (newProject) => {
    isCreateModalOpen.value = false;
    // If on projects list or dashboard, refresh
    if (currentViewComponent.value?.fetchProjects) {
        currentViewComponent.value.fetchProjects();
    } else if (currentViewComponent.value?.fetchSummary) {
        currentViewComponent.value.fetchSummary();
    } else {
        router.push(`/projects/${newProject.id}`);
    }
};

const handleGlobalDataRefresh = () => {
    if (currentViewComponent.value?.fetchProjects) {
        currentViewComponent.value.fetchProjects();
    } else if (currentViewComponent.value?.fetchSummary) {
        currentViewComponent.value.fetchSummary();
    } else if (currentViewComponent.value?.fetchProject) {
        currentViewComponent.value.fetchProject();
    }
};
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
