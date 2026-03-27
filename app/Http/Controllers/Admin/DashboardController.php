<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;

class DashboardController extends Controller
{
    public function loginForm()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required|string']);
        $adminPassword = env('ADMIN_PANEL_PASSWORD', 'nimvo@admin');
        if ($request->password === $adminPassword) {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['password' => 'Senha incorreta.']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_logged_in');
        return redirect()->route('admin.login');
    }

    public function index()
    {
        $totalTenants  = Tenant::count();
        $latestTenants = Tenant::latest()->take(5)->get();
        return view('admin.dashboard', compact('totalTenants', 'latestTenants'));
    }

    public function gitPull(Request $request)
    {
        try {
            $pdvPath = '/var/www/nimvo-app/public/pdv';
            $artisan = '/var/www/nimvo-app/artisan';

            $gitOutput = shell_exec("cd {$pdvPath} && git pull 2>&1") ?? 'sem output';

            $migrateOutput = shell_exec("php {$artisan} tenants:run migrate --option=force=true 2>--option=force 2>&11") ?? 'sem output';

            return response()->json([
                'success' => true,
                'output'  => nl2br(htmlspecialchars($gitOutput)),
                'migrate' => nl2br(htmlspecialchars($migrateOutput)),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'output'  => $e->getMessage(),
                'migrate' => '',
            ], 500);
        }
    }
}
