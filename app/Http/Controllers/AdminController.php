<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// Importe o model de Tenant. Se o caminho for diferente, ajuste.
use App\Models\Tenant; 

class AdminController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $password = $request->input('password');
        $envPassword = env('ADMIN_PANEL_PASSWORD', 'nimvo@admin');

        if ($password === $envPassword) {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['message' => 'Senha incorreta.']);
    }

    public function dashboard()
    {
        // Pegando os dados que a sua view dashboard.blade.php exige
        $totalTenants = 0;
        $latestTenants = [];

        try {
            // Verifica se a classe Tenant existe para evitar erro se o model não estiver pronto
            if (class_exists('App\Models\Tenant')) {
                $totalTenants = Tenant::count();
                $latestTenants = Tenant::latest()->take(5)->get();
            }
        } catch (\Exception $e) {
            Log::error("Erro ao carregar dados do dashboard: " . $e->getMessage());
        }

        return view('admin.dashboard', compact('totalTenants', 'latestTenants'));
    }

    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('admin.login');
    }
}
