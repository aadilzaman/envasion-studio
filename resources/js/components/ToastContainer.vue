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
                class="pointer-events-auto flex items-center justify-between p-4 rounded-2xl shadow-xl border bg-white backdrop-blur-md"
                :class="{
                    'border-emerald-200 text-slate-800': toast.type === 'success',
                    'border-rose-200 text-slate-800': toast.type === 'error',
                    'border-indigo-200 text-slate-800': toast.type === 'info',
                }"
            >
                <div class="flex items-center gap-3">
                    <span v-if="toast.type === 'success'" class="text-emerald-500 text-lg">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>
                    <span v-else-if="toast.type === 'error'" class="text-rose-500 text-lg">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </span>
                    <span v-else class="text-indigo-500 text-lg">
                        <i class="fa-solid fa-circle-info"></i>
                    </span>
                    <p class="text-sm font-semibold text-slate-700">{{ toast.message }}</p>
                </div>
                <button
                    @click="removeToast(toast.id)"
                    class="ml-3 text-slate-400 hover:text-slate-600 transition"
                >
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </transition-group>
    </div>
</template>

<script setup>
import { useToast } from '../services/toast';

const { toasts, removeToast } = useToast();
</script>
