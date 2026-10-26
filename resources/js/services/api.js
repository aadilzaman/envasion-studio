import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

export const apiService = {
    // Dashboard
    getDashboardSummary() {
        return api.get('/dashboard/summary').then(res => res.data);
    },

    // Projects
    getProjects(params = {}) {
        return api.get('/projects', { params }).then(res => res.data);
    },

    getProject(id) {
        return api.get(`/projects/${id}`).then(res => res.data);
    },

    createProject(data) {
        return api.post('/projects', data).then(res => res.data);
    },

    updateProject(id, data) {
        return api.put(`/projects/${id}`, data).then(res => res.data);
    },

    deleteProject(id) {
        return api.delete(`/projects/${id}`).then(res => res.data);
    },

    // Tasks
    getProjectTasks(projectId, params = {}) {
        return api.get(`/projects/${projectId}/tasks`, { params }).then(res => res.data);
    },

    createTask(projectId, data) {
        return api.post(`/projects/${projectId}/tasks`, data).then(res => res.data);
    },

    updateTask(id, data) {
        return api.put(`/tasks/${id}`, data).then(res => res.data);
    },

    toggleTask(id) {
        return api.patch(`/tasks/${id}/toggle`).then(res => res.data);
    },

    deleteTask(id) {
        return api.delete(`/tasks/${id}`).then(res => res.data);
    },

    // Mock Data Reset
    resetMockData() {
        return api.post('/reset-mock-data').then(res => res.data);
    },
};

export default api;
