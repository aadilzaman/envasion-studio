<template>
    <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none max-w-sm w-full">
        <transition-group
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto flex items-center justify-between p-4 rounded-xl shadow-2xl border backdrop-blur-md"
                :class="{
                    'bg-slate-900/95 border-emerald-500/40 text-emerald-400': toast.type === 'success',
                    'bg-slate-900/95 border-rose-500/40 text-rose-400': toast.type === 'error',
                    'bg-slate-900/95 border-indigo-500/40 text-indigo-400': toast.type === 'info',
                }"
            >
                <div class="flex items-center gap-3">
                    <span v-if="toast.type === 'success'" class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span v-else-if="toast.type === 'error'" class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                    <span v-else class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    <p class="text-sm font-medium text-slate-100">{{ toast.message }}</p>
                </div>
                <button
                    @click="removeToast(toast.id)"
                    class="ml-3 text-slate-400 hover:text-slate-200 transition"
                >
                    &times;
                </button>
            </div>
        </transition-group>
    </div>
</template>

<script setup>
import { useToast } from '../services/toast';

const { toasts, removeToast } = useToast();
</script>
