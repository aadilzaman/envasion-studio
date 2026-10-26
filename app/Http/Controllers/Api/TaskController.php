<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks (optionally scoped to a project).
     */
    public function index(Request $request, ?Project $project = null): JsonResponse
    {
        $query = Task::query()->with('project:id,title,status');

        if ($project && $project->exists) {
            $query->where('project_id', $project->id);
        } elseif ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $tasks,
        ]);
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request, ?Project $project = null): JsonResponse
    {
        $data = $request->validated();

        if ($project && $project->exists) {
            $data['project_id'] = $project->id;
        }

        if (!isset($data['status'])) {
            $data['status'] = 'Pending';
        }

        $task = Task::create($data);
        $task->load('project:id,title,status');

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully.',
            'data' => $task,
        ], 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): JsonResponse
    {
        $task->load('project:id,title,status');

        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());
        $task->load('project:id,title,status');

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => $task,
        ]);
    }

    /**
     * Quick toggle completion status for a task.
     */
    public function toggle(Task $task): JsonResponse
    {
        $newStatus = ($task->status === 'Completed') ? 'Pending' : 'Completed';
        $task->update(['status' => $newStatus]);
        $task->load('project:id,title,status');

        return response()->json([
            'success' => true,
            'message' => "Task marked as {$newStatus}.",
            'data' => $task,
        ]);
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $taskTitle = $task->title;
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => "Task '{$taskTitle}' deleted successfully.",
        ]);
    }
}
