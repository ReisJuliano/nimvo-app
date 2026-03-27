@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

<div class="row">

    <!-- Total de Tenants -->
    <div class="col-md-3 col-sm-6">
        <div class="small-box" style="background:#1a1a2e; color:#fff;">
            <div class="inner">
                <h3>{{ $totalTenants }}</h3>
                <p>Tenants Ativos</p>
            </div>
            <div class="icon"><i class="fas fa-building"></i></div>
            <a href="{{ route('admin.tenants.index') }}" class="small-box-footer" style="background:#e63946;">
                Ver todos <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Criar Tenant -->
    <div class="col-md-3 col-sm-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><i class="fas fa-plus" style="font-size:1.8rem;"></i></h3>
                <p>Novo Tenant</p>
            </div>
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <a href="{{ route('admin.tenants.create') }}" class="small-box-footer">
                Criar agora <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Git Pull -->
    <div class="col-md-3 col-sm-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><i class="fab fa-git-alt" style="font-size:1.8rem;"></i></h3>
                <p>Atualizar Sistema</p>
            </div>
            <div class="icon"><i class="fas fa-code-branch"></i></div>
            <a href="#" class="small-box-footer" id="dash-git-pull">
                Git Pull <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

</div>

<!-- Ultimos Tenants -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clock mr-2"></i>Tenants Recentes</span>
                <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-nimvo">Ver Todos</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Criado em</th>
                            <th class="text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestTenants as $tenant)
                        <tr>
                            <td><span class="badge-tenant">{{ $tenant->id }}</span></td>
                            <td>{{ $tenant->data['name'] ?? '-' }}</td>
                            <td>{{ $tenant->data['email'] ?? '-' }}</td>
                            <td>{{ $tenant->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.tenants.show', $tenant->id) }}"
                                   class="btn btn-xs btn-outline-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-xs btn-outline-warning btn-reload-tenant"
                                        data-id="{{ $tenant->id }}">
                                    <i class="fas fa-sync"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Nenhum tenant cadastrado ainda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $('#dash-git-pull').on('click', function (e) {
        e.preventDefault();
        $('#btn-git-pull').trigger('click');
    });
</script>
@endpush
