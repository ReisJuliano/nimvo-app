@extends('admin.layout')

@section('title', 'Tenant: ' . $tenant->id)
@section('page-title', 'Tenant: ' . ($tenant->data['name'] ?? $tenant->id))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Tenants</a></li>
    <li class="breadcrumb-item active">{{ $tenant->id }}</li>
@endsection

@section('content')

<div class="row">

    <!-- Info do Tenant -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle mr-2"></i>Informacoes
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" width="110">ID</td>
                        <td><span class="badge-tenant">{{ $tenant->id }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nome</td>
                        <td>{{ $tenant->data['name'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">E-mail</td>
                        <td>{{ $tenant->data['email'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Subdominio</td>
                        <td><a href="https://{{ $tenant->id }}.nimvo.com.br" target="_blank">
                            {{ $tenant->id }}.nimvo.com.br
                            <i class="fas fa-external-link-alt fa-xs ml-1"></i>
                        </a></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Banco</td>
                        <td><code>tenant_{{ $tenant->id }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Criado em</td>
                        <td>{{ $tenant->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
            <div class="card-footer d-flex gap-2 justify-content-between">
                <button class="btn btn-sm btn-outline-warning btn-reload-tenant w-50" data-id="{{ $tenant->id }}">
                    <i class="fas fa-sync mr-1"></i>Migrate
                </button>
                <button class="btn btn-sm btn-outline-danger btn-delete-tenant w-50"
                        data-id="{{ $tenant->id }}"
                        data-name="{{ $tenant->data['name'] ?? $tenant->id }}">
                    <i class="fas fa-trash mr-1"></i>Deletar
                </button>
            </div>
        </div>
    </div>

    <!-- Tabelas do Banco -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-table mr-2"></i>Tabelas do Banco ({{ count($tables) }})</span>
                <button class="btn btn-xs btn-outline-warning btn-reload-tenant" data-id="{{ $tenant->id }}">
                    <i class="fas fa-sync mr-1"></i>Rodar Migrate
                </button>
            </div>
            <div class="card-body p-0" style="max-height:520px; overflow-y:auto;">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light sticky-top">
                        <tr>
                            <th>#</th>
                            <th>Nome da Tabela</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tables as $i => $table)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td><i class="fas fa-table text-muted mr-2 fa-xs"></i><code>{{ $table }}</code></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-4">
                                Nenhuma tabela encontrada. Rode o migrate para criar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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

<!-- Modal resultado reload -->
<div class="modal fade" id="modal-reload-result" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e; color:#fff;">
                <h5 class="modal-title"><i class="fas fa-sync mr-2"></i>Resultado do Migrate</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0">
                <pre class="output-box mb-0 rounded-0" id="reload-output">Aguardando...</pre>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-secondary" data-dismiss="modal">Fechar</button>
                <button class="btn btn-sm btn-nimvo" onclick="location.reload()">
                    <i class="fas fa-redo mr-1"></i>Recarregar Pagina
                </button>
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

    // Override reload para mostrar resultado inline
    $(document).off('click', '.btn-reload-tenant');
    $(document).on('click', '.btn-reload-tenant', function () {
        const btn  = $(this);
        const id   = btn.data('id');
        const icon = btn.find('i');

        icon.removeClass('fa-sync').addClass('fa-spinner spinning');
        btn.prop('disabled', true);

        $('#reload-output').text('Executando migrate...');
        $('#modal-reload-result').modal('show');

        $.ajax({
            url: `/tenants/${id}/reload`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                $('#reload-output').html(res.output || 'Concluido sem saida.');
            },
            error: function () {
                $('#reload-output').text('Erro ao executar migrate.');
            },
            complete: function () {
                icon.removeClass('fa-spinner spinning').addClass('fa-sync');
                btn.prop('disabled', false);
            }
        });
    });
</script>
@endpush
