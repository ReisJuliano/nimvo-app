<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | Nimvo</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">

    <style>
        .brand-link { background: #1a1a2e; }
        .main-sidebar, .main-sidebar::before { background: #1a1a2e; }
        .nav-sidebar .nav-item>.nav-link.active { background: #e63946; color: #fff; }
        .nav-sidebar .nav-link:hover { background: rgba(255,255,255,.08); }
        .nav-sidebar .nav-link { color: #c8c8d0; }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active { background: #e63946; }
        .content-wrapper { background: #f4f6f9; }
        .card { box-shadow: 0 1px 3px rgba(0,0,0,.12); border: none; border-radius: 6px; }
        .card-header { border-radius: 6px 6px 0 0 !important; font-weight: 600; }
        .btn-nimvo { background: #e63946; border-color: #e63946; color: #fff; }
        .btn-nimvo:hover { background: #c1121f; border-color: #c1121f; color: #fff; }
        .badge-tenant { background: #1a1a2e; color: #fff; font-size: .7rem; padding: 3px 8px; border-radius: 4px; }
        #toast-container { position: fixed; bottom: 20px; right: 20px; z-index: 9999; }
        .toast-box {
            background: #1a1a2e; color: #fff; padding: 14px 20px;
            border-radius: 6px; margin-top: 8px; min-width: 280px;
            display: flex; align-items: flex-start; gap: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,.2);
            animation: slideIn .25s ease;
        }
        .toast-box.success { border-left: 4px solid #28a745; }
        .toast-box.error   { border-left: 4px solid #e63946; }
        @keyframes slideIn { from { transform: translateX(60px); opacity:0; } to { transform: translateX(0); opacity:1; } }
        pre.output-box {
            background: #1a1a2e; color: #a8ff78; border-radius: 6px;
            padding: 14px; font-size: .8rem; max-height: 300px; overflow-y: auto;
        }
        .git-btn { min-width: 140px; }
        .spinning { animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark navbar-dark" style="background:#1a1a2e; border-bottom:2px solid #e63946;">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <!-- Git Pull -->
            <li class="nav-item mr-3">
                <button class="btn btn-sm btn-nimvo git-btn" id="btn-git-pull">
                    <i class="fab fa-git-alt mr-1"></i> Git Pull
                </button>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">
                        <i class="fas fa-sign-out-alt mr-1"></i> Sair
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-1">
        <a href="{{ route('admin.dashboard') }}" class="brand-link text-center" style="border-bottom:1px solid #2d2d4e;">
            <span class="brand-text font-weight-bold" style="color:#e63946; font-size:1.3rem;">NIMVO</span>
            <small class="d-block" style="color:#888; font-size:.65rem; margin-top:-4px;">Painel Administrativo</small>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-compact" data-widget="treeview" role="menu">

                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header" style="color:#555; font-size:.65rem;">TENANTS</li>

                    <li class="nav-item">
                        <a href="{{ route('admin.tenants.index') }}" class="nav-link {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Gerenciar Tenants</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.tenants.create') }}" class="nav-link">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Novo Tenant</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0" style="font-size:1.3rem; font-weight:600;">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">

                {{-- Alerts --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle mr-2"></i>{!! session('success') !!}
                        @if(session('output'))
                            <hr>
                            <pre class="output-box mb-0">{!! session('output') !!}</pre>
                        @endif
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle mr-2"></i>{!! session('error') !!}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer text-center" style="font-size:.78rem;">
        <strong>Nimvo Admin</strong> &copy; {{ date('Y') }}
    </footer>

</div>

<!-- Toast container -->
<div id="toast-container"></div>

<!-- Git Pull Modal -->
<div class="modal fade" id="modal-git" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e; color:#fff;">
                <h5 class="modal-title"><i class="fab fa-git-alt mr-2"></i>Git Pull - Resultado</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0">
                <pre class="output-box mb-0 rounded-0" id="git-output" style="min-height:100px;">Aguardando...</pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

<script>
    // ----------------------------------------------------------------
    // Toast
    // ----------------------------------------------------------------
    function showToast(msg, type = 'success') {
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const color = type === 'success' ? '#28a745' : '#e63946';
        const el = $(`<div class="toast-box ${type}">
            <i class="fas ${icon}" style="color:${color}; margin-top:2px;"></i>
            <span>${msg}</span>
        </div>`);
        $('#toast-container').append(el);
        setTimeout(() => el.fadeOut(400, () => el.remove()), 4000);
    }

    // ----------------------------------------------------------------
    // Git Pull
    // ----------------------------------------------------------------
    $('#btn-git-pull').on('click', function () {
        const btn = $(this);
        const icon = btn.find('i');
        icon.removeClass('fa-git-alt').addClass('fa-spinner spinning');
        btn.prop('disabled', true);

        $('#git-output').html('Executando git pull...');
        $('#modal-git').modal('show');

        $.ajax({
            url: '{{ route("admin.git.pull") }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                $('#git-output').html(res.output || 'Concluido.');
                if (res.migrate) {
                    $('#git-output').append('\n\n--- Migrations ---\n' + res.migrate);
                }
                showToast('Git pull executado com sucesso.');
            },
            error: function () {
                $('#git-output').html('Erro ao executar git pull.');
                showToast('Erro ao executar git pull.', 'error');
            },
            complete: function () {
                icon.removeClass('fa-spinner spinning').addClass('fa-git-alt');
                btn.prop('disabled', false);
            }
        });
    });

    // ----------------------------------------------------------------
    // Reload tenant (migrate)
    // ----------------------------------------------------------------
    $(document).on('click', '.btn-reload-tenant', function () {
        const btn = $(this);
        const id  = btn.data('id');
        const icon = btn.find('i');

        if (!confirm(`Rodar migrate no tenant "${id}"?`)) return;

        icon.removeClass('fa-sync').addClass('fa-spinner spinning');
        btn.prop('disabled', true);

        $.ajax({
            url: `/tenants/${id}/reload`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                showToast(`Tenant <strong>${id}</strong> atualizado.`);
            },
            error: function () {
                showToast('Erro ao recarregar tenant.', 'error');
            },
            complete: function () {
                icon.removeClass('fa-spinner spinning').addClass('fa-sync');
                btn.prop('disabled', false);
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>
