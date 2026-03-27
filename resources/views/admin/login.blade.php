<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Nimvo Admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <style>
        body { background: #1a1a2e; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card {
            background: #fff; border-radius: 10px; padding: 40px 36px;
            width: 100%; max-width: 380px;
            box-shadow: 0 8px 32px rgba(0,0,0,.4);
        }
        .login-logo { text-align: center; margin-bottom: 28px; }
        .login-logo span { color: #e63946; font-size: 2rem; font-weight: 700; letter-spacing: 2px; }
        .login-logo small { display: block; color: #888; font-size: .75rem; margin-top: -4px; }
        .btn-nimvo { background: #e63946; border-color: #e63946; color: #fff; font-weight: 600; }
        .btn-nimvo:hover { background: #c1121f; border-color: #c1121f; color: #fff; }
        .form-control:focus { border-color: #e63946; box-shadow: 0 0 0 .2rem rgba(230,57,70,.2); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <span>NIMVO</span>
            <small>Painel Administrativo</small>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label class="font-weight-600" style="font-size:.85rem;">Senha de Acesso</label>
                <div class="input-group">
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Digite a senha" autofocus required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="toggle-pw" style="cursor:pointer;">
                            <i class="fas fa-eye-slash" id="eye-icon"></i>
                        </span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-nimvo btn-block mt-3">
                <i class="fas fa-sign-in-alt mr-2"></i>Entrar
            </button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
        $('#toggle-pw').on('click', function () {
            const pw   = $('#password');
            const icon = $('#eye-icon');
            if (pw.attr('type') === 'password') {
                pw.attr('type', 'text');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                pw.attr('type', 'password');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });
    </script>
</body>
</html>
