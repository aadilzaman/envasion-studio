<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects with optional status and search filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::query()
            ->withCount('tasks')
            ->withCount(['tasks as completed_tasks_count' => function ($q) {
                $q->where('status', 'Completed');
            }]);

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        // Search in title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['id', 'title', 'status', 'deadline', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, strtolower($sortOrder) === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $projects = $query->get();

        return response()->json([
            'success' => true,
            'data' => $projects,
        ]);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());
        $project->loadCount('tasks');
        $project->loadCount(['tasks as completed_tasks_count' => function ($q) {
            $q->where('status', 'Completed');
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully.',
            'data' => $project,
        ], 201);
    }

    /**
     * Display the specified project with its tasks.
     */
    public function show(Project $project): JsonResponse
    {
        $project->load(['tasks' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }]);

        $project->loadCount('tasks');
        $project->loadCount(['tasks as completed_tasks_count' => function ($q) {
            $q->where('status', 'Completed');
        }]);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    /**
     * Update the specified project in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());

        $project->loadCount('tasks');
        $project->loadCount(['tasks as completed_tasks_count' => function ($q) {
            $q->where('status', 'Completed');
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully.',
            'data' => $project,
        ]);
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project): JsonResponse
    {
        $projectTitle = $project->title;
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => "Project '{$projectTitle}' deleted successfully.",
        ]);
    }
}
