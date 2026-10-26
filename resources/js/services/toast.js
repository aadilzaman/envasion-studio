import { reactive } from 'vue';

export const toastState = reactive({
    toasts: [],
});

let nextId = 1;

export function useToast() {
    const showToast = (message, type = 'success', duration = 3500) => {
        const id = nextId++;
        toastState.toasts.push({ id, message, type });

        setTimeout(() => {
            removeToast(id);
        }, duration);
    };

    const removeToast = (id) => {
        const index = toastState.toasts.findIndex(t => t.id === id);
        if (index !== -1) {
            toastState.toasts.splice(index, 1);
        }
    };

    return {
        toasts: toastState.toasts,
        showToast,
        removeToast,
        success: (msg) => showToast(msg, 'success'),
        error: (msg) => showToast(msg, 'error'),
        info: (msg) => showToast(msg, 'info'),
    };
}
