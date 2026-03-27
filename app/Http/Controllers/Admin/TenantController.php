<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;

class TenantController extends Controller
{
    // ----------------------------------------------------------------
    // Listar Tenants
    // ----------------------------------------------------------------

    public function index(Request $request)
    {
        $query = Tenant::query();

        if ($search = $request->input('search')) {
            $query->where('id', 'like', "%{$search}%")
                  ->orWhereJsonContains('data->name', $search)
                  ->orWhereJsonContains('data->email', $search);
        }

        $tenants = $query->latest()->paginate(20)->withQueryString();

        return view('admin.tenants.index', compact('tenants'));
    }

    // ----------------------------------------------------------------
    // Formulario de Criacao
    // ----------------------------------------------------------------

    public function create()
    {
        return view('admin.tenants.create');
    }

    // ----------------------------------------------------------------
    // Criar Tenant
    // ----------------------------------------------------------------

    public function store(Request $request)
    {
        $request->validate([
            'id'    => 'required|string|alpha_dash|max:63|unique:tenants,id',
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $id    = strtolower($request->id);
        $name  = $request->name;
        $email = $request->email;

        $appPath = '/var/www/nimvo-app';

        $output = shell_exec(
            "cd {$appPath} && php artisan tenant:create {$id} --name=\"{$name}\" --email=\"{$email}\" 2>&1"
        );

        return redirect()->route('admin.tenants.index')->with([
            'success' => "Tenant <strong>{$id}</strong> criado com sucesso.",
            'output'  => nl2br(htmlspecialchars($output ?? '')),
        ]);
    }

    // ----------------------------------------------------------------
    // Ver detalhes do Tenant
    // ----------------------------------------------------------------

    public function show(string $id)
    {
        $tenant = Tenant::findOrFail($id);

        // Conectar ao banco do tenant para listar tabelas
        tenancy()->initialize($tenant);

        $tables = collect(\DB::select('SHOW TABLES'))->map(function ($t) {
            $t = (array) $t;
            return array_values($t)[0];
        });

        tenancy()->end();

        return view('admin.tenants.show', compact('tenant', 'tables'));
    }

    // ----------------------------------------------------------------
    // Deletar Tenant
    // ----------------------------------------------------------------

    public function destroy(string $id)
    {
        $tenant = Tenant::findOrFail($id);

        $appPath = '/var/www/nimvo-app';
        $output  = shell_exec("cd {$appPath} && php artisan tenant:delete {$id} 2>&1");

        return redirect()->route('admin.tenants.index')->with([
            'success' => "Tenant <strong>{$id}</strong> removido.",
            'output'  => nl2br(htmlspecialchars($output ?? '')),
        ]);
    }

    // ----------------------------------------------------------------
    // Reload (migrate + seed) em um tenant
    // ----------------------------------------------------------------

    public function reload(string $id)
    {
        $tenant = Tenant::findOrFail($id);

        tenancy()->initialize($tenant);
        Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = Artisan::output();
        tenancy()->end();

        return response()->json([
            'success' => true,
            'output'  => nl2br(htmlspecialchars($migrateOutput)),
        ]);
    }
}
