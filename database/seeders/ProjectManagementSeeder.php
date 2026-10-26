<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ProjectManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Task::truncate();
        Project::truncate();
        Schema::enableForeignKeyConstraints();

        $jsonPath = database_path('data/mock-data.json');
        
        if (File::exists($jsonPath)) {
            $data = json_decode(File::get($jsonPath), true);
        } else {
            $data = $this->defaultMockData();
        }

        // Seed Projects
        foreach ($data['projects'] as $projectData) {
            Project::create([
                'id' => $projectData['id'],
                'title' => $projectData['title'],
                'description' => $projectData['description'] ?? null,
                'status' => $projectData['status'] ?? 'Active',
                'deadline' => $projectData['deadline'] ?? null,
            ]);
        }

        // Seed Tasks
        foreach ($data['tasks'] as $taskData) {
            Task::create([
                'id' => $taskData['id'],
                'project_id' => $taskData['projectId'] ?? $taskData['project_id'],
                'title' => $taskData['title'],
                'description' => $taskData['description'] ?? null,
                'status' => $taskData['status'] ?? 'Pending',
            ]);
        }
    }

    /**
     * Fallback mock data in case the json file is missing.
     */
    private function defaultMockData(): array
    {
        return [
            'projects' => [
                [
                    'id' => 1,
                    'title' => 'E‑Commerce Website Redesign',
                    'description' => 'Complete redesign of an online store with modern UI and improved checkout flow.',
                    'status' => 'Active',
                    'deadline' => '2026-08-15',
                ],
                [
                    'id' => 2,
                    'title' => 'Mobile App for Fitness Tracking',
                    'description' => 'Cross‑platform mobile app to track workouts, nutrition, and progress.',
                    'status' => 'Active',
                    'deadline' => '2026-09-01',
                ],
            ],
            'tasks' => [
                [
                    'id' => 101,
                    'projectId' => 1,
                    'title' => 'Design homepage mockup',
                    'description' => 'Create high‑fidelity Figma design for the new homepage.',
                    'status' => 'Completed',
                ],
            ],
        ];
    }
}
