<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCreationDebugTest extends TestCase
{
    use RefreshDatabase;

    public function test_debug_task_creation()
    {
        // Criar usuários de teste
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        // Criar tarefa
        $response = $this->actingAs($admin)
            ->postJson('/api/tasks', [
                'title' => 'Debug Task',
                'description' => 'Test description',
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'user_id' => $user->id,
            ]);

        // Debug da resposta
        echo "Status: " . $response->getStatusCode() . "\n";
        echo "Content: " . $response->getContent() . "\n";

        $response->assertStatus(201);
        
        // Verificar estrutura retornada
        $json = $response->json();
        $this->assertArrayHasKey('completed', $json, 'Response JSON: ' . json_encode($json));
    }
}
