<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteTenant extends Command
{
    protected $signature = 'tenant:delete {id : Identificador do tenant}';
    protected $description = 'Remove um tenant, seu banco de dados e subdomínio';

    public function handle()
    {
        $id = $this->argument('id');

        if (!DB::table('tenants')->where('id', $id)->exists()) {
            $this->error("Tenant '$id' não encontrado!");
            return 1;
        }

        if (!$this->confirm("Tem certeza que deseja deletar o tenant '$id'? Isso é irreversível!")) {
            $this->info('Operação cancelada.');
            return 0;
        }

        $dbName = 'tenant' . $id;

        DB::statement("DROP DATABASE IF EXISTS `$dbName`");
        DB::table('domains')->where('domain', $id . '.nimvo.com.br')->delete();
        DB::table('tenants')->where('id', $id)->delete();

        $this->info("✅ Tenant '$id' removido com sucesso!");
        return 0;
    }
}
