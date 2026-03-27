<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('tenant_user')) {
            return redirect()->route('tenant.dashboard');
        }
        return view('tenant.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Preencha todos os campos.',
            'password.required' => 'Preencha todos os campos.',
        ]);

        $user = DB::table('users')
            ->where('username', $request->username)
            ->where('active', 1)
            ->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Usuário ou senha incorretos.'])->withInput();
        }

        session([
            'tenant_user' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'role'     => $user->role,
            ]
        ]);

        if (!empty($user->must_change_password)) {
            return redirect()->route('tenant.change-password');
        }

        return redirect()->route('tenant.dashboard');
    }

    public function logout()
    {
        session()->forget('tenant_user');
        return redirect()->route('tenant.login');
    }
}