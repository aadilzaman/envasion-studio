<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_retrieve_dashboard_summary(): void
    {
        $response = $this->getJson('/api/dashboard/summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_projects',
                    'active_projects',
                    'completed_tasks',
                    'pending_tasks',
                ],
            ]);
    }

    public function test_can_list_projects(): void
    {
        $response = $this->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertNotEmpty($response->json('data'));
    }

    public function test_can_filter_projects_by_status(): void
    {
        $response = $this->getJson('/api/projects?status=Completed');

        $response->assertStatus(200);
        $data = $response->json('data');
        foreach ($data as $project) {
            $this->assertEquals('Completed', $project['status']);
        }
    }

    public function test_can_create_a_project(): void
    {
        $payload = [
            'title' => 'Brand New AI Agent Studio',
            'description' => 'A cutting edge AI automation platform',
            'status' => 'Active',
            'deadline' => '2026-12-31',
        ];

        $response = $this->postJson('/api/projects', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Brand New AI Agent Studio');

        $this->assertDatabaseHas('projects', ['title' => 'Brand New AI Agent Studio']);
    }

    public function test_can_update_a_project(): void
    {
        $project = Project::first();

        $response = $this->putJson("/api/projects/{$project->id}", [
            'title' => 'Updated Project Title',
            'status' => 'Completed',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Project Title')
            ->assertJsonPath('data.status', 'Completed');
    }

    public function test_can_delete_a_project(): void
    {
        $project = Project::first();

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_can_manage_tasks_for_project(): void
    {
        $project = Project::first();

        // Create Task
        $response = $this->postJson("/api/projects/{$project->id}/tasks", [
            'title' => 'Write Automated Unit Tests',
            'description' => 'Ensure 100% test coverage',
            'status' => 'Pending',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Write Automated Unit Tests');

        $taskId = $response->json('data.id');

        // Toggle task
        $toggleResponse = $this->patchJson("/api/tasks/{$taskId}/toggle");
        $toggleResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'Completed');

        // Delete task
        $deleteResponse = $this->deleteJson("/api/tasks/{$taskId}");
        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }
}
