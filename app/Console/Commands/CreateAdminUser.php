<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
                           {--name= : Nome do administrador}
                           {--email= : Email do administrador}
                           {--password= : Senha do administrador}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar um usuário administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Criação de Usuário Administrador ===');

        // Obter nome
        $name = $this->option('name') ?: $this->ask('Nome do administrador');
        
        // Obter email
        $email = $this->option('email') ?: $this->ask('Email do administrador');
        
        // Validar email
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            $this->error('Email inválido ou já existe no sistema.');
            return 1;
        }

        // Obter senha
        $password = $this->option('password') ?: $this->secret('Senha do administrador (mínimo 8 caracteres)');

        // Validar senha
        if (strlen($password) < 8) {
            $this->error('A senha deve ter pelo menos 8 caracteres.');
            return 1;
        }

        // Confirmar criação
        if (!$this->confirm("Criar administrador '$name' com email '$email'?")) {
            $this->info('Operação cancelada.');
            return 0;
        }

        // Criar usuário
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            $this->info("✅ Administrador criado com sucesso!");
            $this->table(['Campo', 'Valor'], [
                ['ID', $user->id],
                ['Nome', $user->name],
                ['Email', $user->email],
                ['Tipo', 'Administrador'],
                ['Criado em', $user->created_at->format('d/m/Y H:i:s')]
            ]);

            return 0;
        } catch (\Exception $e) {
            $this->error("Erro ao criar administrador: " . $e->getMessage());
            return 1;
        }
    }
}
