<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create 
                           {--name= : Nome do administrador}
                           {--email= : Email do administrador}
                           {--password= : Senha do administrador}';

    protected $description = 'Criar um usuário administrador';


    public function handle()
    {
        $this->info('=== Criação de Usuário Administrador ===');

        $name = $this->option('name') ?: $this->ask('Nome do administrador');
        
        $email = $this->option('email') ?: $this->ask('Email do administrador');
        
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            $this->error('Email inválido ou já existe no sistema.');
            return 1;
        }

        $password = $this->option('password') ?: $this->secret('Senha do administrador (mínimo 8 caracteres)');

        if (strlen($password) < 8) {
            $this->error('A senha deve ter pelo menos 8 caracteres.');
            return 1;
        }


        if (!$this->confirm("Criar administrador '$name' com email '$email'?")) {
            $this->info('Operação cancelada.');
            return 0;
        }

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
