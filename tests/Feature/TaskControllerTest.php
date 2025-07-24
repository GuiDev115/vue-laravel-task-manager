<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_create_task(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($admin)
            ->postJson('/api/tasks', [
                'title' => 'Nova Tarefa',
                'description' => 'Descrição da tarefa',
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'user_id' => $user->id,
            ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id',
                    'title',
                    'description',
                    'due_date',
                    'completed',
                    'user_id',
                    'user'
                ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Nova Tarefa',
            'user_id' => $user->id,
        ]);
    }

    public function test_regular_user_cannot_create_task(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)
            ->postJson('/api/tasks', [
                'title' => 'Nova Tarefa',
                'description' => 'Descrição da tarefa',
                'user_id' => $user->id,
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_view_own_tasks(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)
            ->getJson('/api/tasks');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'due_date',
                            'completed',
                            'user_id',
                            'user'
                        ]
                    ],
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]);
    }

    public function test_user_can_toggle_own_task(): void
    {
        $user = User::where('role', 'user')->first();
        $task = $user->tasks()->first();

        $originalStatus = $task->completed;

        $response = $this->actingAs($user)
            ->patchJson("/api/tasks/{$task->id}/toggle");

        $response->assertStatus(200);
        
        $task->refresh();
        $this->assertNotEquals($originalStatus, $task->completed);
    }

    public function test_user_cannot_toggle_others_task(): void
    {
        $user1 = User::where('role', 'user')->first();
        $user2 = User::where('role', 'user')->where('id', '!=', $user1->id)->first();
        
        $task = $user2->tasks()->first();

        $response = $this->actingAs($user1)
            ->patchJson("/api/tasks/{$task->id}/toggle");

        $response->assertStatus(403);
    }

    public function test_admin_can_view_all_tasks(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)
            ->getJson('/api/tasks');

        $response->assertStatus(200);
        
        $totalTasks = Task::count();
        $this->assertEquals($totalTasks, count($response->json('data')));
    }

    public function test_task_search_functionality(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)
            ->getJson('/api/tasks?search=sistema');

        $response->assertStatus(200);
        
        foreach ($response->json('data') as $task) {
            $this->assertTrue(
                str_contains(strtolower($task['title']), 'sistema') ||
                str_contains(strtolower($task['description'] ?? ''), 'sistema')
            );
        }
    }

    public function test_task_status_filter(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)
            ->getJson('/api/tasks?status=completed');

        $response->assertStatus(200);
        
        foreach ($response->json('data') as $task) {
            $this->assertTrue($task['completed']);
        }
    }
}
