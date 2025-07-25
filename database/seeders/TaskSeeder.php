<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{

    public function run(): void
    {
        $users = User::all();

        $tasks = [
            [
                'title' => 'Desenvolver sistema de autenticação',
                'description' => 'Implementar login, registro e recuperação de senha',
                'due_date' => now()->addDays(5),
                'completed' => false,
            ],
            [
                'title' => 'Criar interface de usuário',
                'description' => 'Desenvolver componentes Vue.js para o frontend',
                'due_date' => now()->addDays(10),
                'completed' => false,
            ],
            [
                'title' => 'Implementar API REST',
                'description' => 'Criar endpoints para CRUD de tarefas',
                'due_date' => now()->addDays(7),
                'completed' => true,
            ],
            [
                'title' => 'Configurar banco de dados',
                'description' => 'Configurar MySQL e migrations',
                'due_date' => now()->subDays(2),
                'completed' => true,
            ],
            [
                'title' => 'Implementar testes unitários',
                'description' => 'Escrever testes para controllers e models',
                'due_date' => now()->addDays(15),
                'completed' => false,
            ],
            [
                'title' => 'Deploy da aplicação',
                'description' => 'Configurar servidor e deploy em produção',
                'due_date' => now()->addDays(20),
                'completed' => false,
            ],
            [
                'title' => 'Documentação do projeto',
                'description' => 'Escrever README e documentação da API',
                'due_date' => now()->addDays(12),
                'completed' => false,
            ],
            [
                'title' => 'Otimização de performance',
                'description' => 'Melhorar velocidade de carregamento',
                'due_date' => now()->addDays(25),
                'completed' => false,
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create([
                ...$taskData,
                'user_id' => $users->random()->id,
            ]);
        }
    }
}
