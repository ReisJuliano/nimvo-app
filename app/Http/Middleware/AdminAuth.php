<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // ✅ importar Log

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // ⚡ Adicione esta linha para registrar o acesso
        Log::info('AdminAuth acionado na rota: ' . $request->path());

        // Rotas de login não precisam de autenticação
        if ($request->routeIs('admin.login') || $request->routeIs('admin.login.post')) {
            return $next($request);
        }

        if (!session('admin_logged_in')) {
            Log::info('Admin não logado, redirecionando para login');
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
