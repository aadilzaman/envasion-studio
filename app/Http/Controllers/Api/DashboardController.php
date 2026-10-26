<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Get aggregate metrics and summary data for the dashboard.
     */
    public function summary(): JsonResponse
    {
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'Active')->count();
        $completedProjects = Project::where('status', 'Completed')->count();
        $onHoldProjects = Project::where('status', 'On Hold')->count();

        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'Completed')->count();
        $pendingTasks = Task::where('status', 'Pending')->count();
        $inProgressTasks = Task::where('status', 'In Progress')->count();

        // Task completion percentage
        $taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        // Recent projects with progress
        $recentProjects = Project::query()
            ->withCount('tasks')
            ->withCount(['tasks as completed_tasks_count' => function ($q) {
                $q->where('status', 'Completed');
            }])
            ->latest()
            ->take(5)
            ->get();

        // Projects with upcoming deadlines
        $upcomingDeadlines = Project::query()
            ->whereNotNull('deadline')
            ->where('status', '!=', 'Completed')
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_projects' => $totalProjects,
                'active_projects' => $activeProjects,
                'completed_projects' => $completedProjects,
                'on_hold_projects' => $onHoldProjects,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'in_progress_tasks' => $inProgressTasks,
                'task_completion_rate' => $taskCompletionRate,
                'recent_projects' => $recentProjects,
                'upcoming_deadlines' => $upcomingDeadlines,
            ],
        ]);
    }
}
