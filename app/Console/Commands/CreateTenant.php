<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTenant extends Command
{
    protected $signature = 'tenant:create 
                            {id : Identificador único do tenant (ex: clinicaabc)}
                            {--name= : Nome do cliente}
                            {--email= : Email do cliente}';

    protected $description = 'Cria um novo tenant com banco de dados e subdomínio';

    public function handle()
    {
        $id = $this->argument('id');
        $name = $this->option('name') ?? $id;
        $email = $this->option('email');

        $this->info("🚀 Criando tenant: $id");

        // 1. Verifica se já existe
        if (Tenant::find($id)) {
            $this->error("Tenant '$id' já existe!");
            return 1;
        }

        // 2. Cria o tenant
        $this->info("📋 Registrando tenant no banco central...");
        $tenant = Tenant::create([
            'id' => $id,
            'data' => ['name' => $name, 'email' => $email],
        ]);

        // 3. Registra o subdomínio
        $this->info("🌐 Registrando subdomínio: $id.nimvo.com.br...");
        $tenant->domains()->create(['domain' => $id . '.nimvo.com.br']);

        // 4. Cria o banco de dados
        $dbName = 'tenant' . $id;
        $this->info("🗄️  Criando banco de dados: $dbName...");
        DB::statement("CREATE DATABASE IF NOT EXISTS `$dbName`");

        // 5. Roda as migrations no tenant
        $this->info("⚙️  Rodando migrations...");
        tenancy()->initialize($tenant);
        \Artisan::call('migrate', ['--force' => true]);
        $this->line(\Artisan::output());
        tenancy()->end();

        $this->info("✅ Tenant criado com sucesso!");
        $this->table(
            ['Campo', 'Valor'],
            [
                ['ID', $id],
                ['Nome', $name],
                ['Email', $email ?? '-'],
                ['URL', "https://$id.nimvo.com.br"],
                ['Banco', $dbName],
            ]
        );

        return 0;
    }
}
