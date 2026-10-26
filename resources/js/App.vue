<template>
    <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans selection:bg-indigo-500 selection:text-white">
        <!-- Modern Left Sidebar (replaces the top navbar) -->
        <Sidebar
            @open-create-project="isCreateModalOpen = true"
            @data-refreshed="handleGlobalDataRefresh"
        />

        <!-- Main Content Wrapper (Shifted right on desktop to accommodate the sidebar) -->
        <div class="lg:pl-64 flex-1 flex flex-col min-h-screen">
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

            <!-- Agency Clean Light Footer -->
            <footer class="border-t border-slate-200 bg-white py-6 text-xs text-slate-500">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="font-medium text-slate-600">EnvisionStudio • Web Developer Practical Assessment</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 text-[11px] font-mono text-slate-600">
                            Laravel 12 + Vue 3 Light UI
                        </span>
                        <a
                            href="/api/projects"
                            target="_blank"
                            class="text-indigo-600 hover:text-indigo-800 font-semibold transition"
                        >
                            REST API JSON
                        </a>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Global Project Modal for "+ New Project" action -->
        <ProjectModal
            :is-open="isCreateModalOpen"
            :project="null"
            @close="isCreateModalOpen = false"
            @saved="handleGlobalProjectSaved"
        />

        <!-- Toast Notifications -->
        <ToastContainer />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Sidebar from './components/Sidebar.vue';
import ProjectModal from './components/ProjectModal.vue';
import ToastContainer from './components/ToastContainer.vue';

const router = useRouter();
const isCreateModalOpen = ref(false);
const currentViewComponent = ref(null);

const handleGlobalProjectSaved = (newProject) => {
    isCreateModalOpen.value = false;
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
