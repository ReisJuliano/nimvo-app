@extends('admin.layout')

@section('title', 'Tenants')
@section('page-title', 'Gerenciar Tenants')
@section('breadcrumb')
    <li class="breadcrumb-item active">Tenants</li>
@endsection

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-building mr-2"></i>Todos os Tenants</span>
        <a href="{{ route('admin.tenants.create') }}" class="btn btn-sm btn-nimvo">
            <i class="fas fa-plus mr-1"></i> Novo Tenant
        </a>
    </div>

    <div class="card-body pb-0">
        <form method="GET" action="{{ route('admin.tenants.index') }}" class="mb-3">
            <div class="input-group" style="max-width:360px;">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Buscar por ID, nome ou e-mail..."
                       value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-sm btn-nimvo" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th width="160">ID / Subdominio</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Criado em</th>
                    <th class="text-right" width="160">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr>
                    <td>
                        <span class="badge-tenant">{{ $tenant->id }}</span>
                        <br>
                        <small class="text-muted">{{ $tenant->id }}.nimvo.com.br</small>
                    </td>
                    <td>{{ $tenant->data['name'] ?? '-' }}</td>
                    <td>{{ $tenant->data['email'] ?? '-' }}</td>
                    <td>{{ $tenant->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-right">
                        <a href="{{ route('admin.tenants.show', $tenant->id) }}"
                           class="btn btn-xs btn-outline-info" title="Ver detalhes">
                            <i class="fas fa-table mr-1"></i>Tabelas
                        </a>
                        <button class="btn btn-xs btn-outline-warning btn-reload-tenant"
                                data-id="{{ $tenant->id }}" title="Rodar migrate">
                            <i class="fas fa-sync"></i>
                        </button>
                        <button class="btn btn-xs btn-outline-danger btn-delete-tenant"
                                data-id="{{ $tenant->id }}"
                                data-name="{{ $tenant->data['name'] ?? $tenant->id }}"
                                title="Deletar tenant">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        Nenhum tenant encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tenants->hasPages())
    <div class="card-footer">
        {{ $tenants->links() }}
    </div>
    @endif
</div>

<!-- Modal confirmar exclusao -->
<div class="modal fade" id="modal-delete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Confirmar</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <p>Deletar o tenant <strong id="delete-tenant-name"></strong>?</p>
                <small class="text-muted">Esta acao e irreversivel. O banco de dados sera removido.</small>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
                <form id="form-delete" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash mr-1"></i>Deletar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).on('click', '.btn-delete-tenant', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');
        $('#delete-tenant-name').text(name);
        $('#form-delete').attr('action', `/tenants/${id}`);
        $('#modal-delete').modal('show');
    });
</script>
@endpush
