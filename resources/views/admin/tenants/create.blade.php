@extends('admin.layout')

@section('title', 'Novo Tenant')
@section('page-title', 'Criar Novo Tenant')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Tenants</a></li>
    <li class="breadcrumb-item active">Novo</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-plus-circle mr-2"></i>Dados do Novo Tenant
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('admin.tenants.store') }}">
                    @csrf

                    <!-- ID -->
                    <div class="form-group">
                        <label>ID / Subdominio <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="id"
                                   class="form-control @error('id') is-invalid @enderror"
                                   value="{{ old('id') }}"
                                   placeholder="meucliente"
                                   pattern="[a-z0-9\-]+"
                                   maxlength="63"
                                   required>
                            <div class="input-group-append">
                                <span class="input-group-text">.nimvo.com.br</span>
                            </div>
                            @error('id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            Apenas letras minusculas, numeros e hifen. Este sera o subdominio do cliente.
                        </small>
                        <div class="mt-1">
                            <span class="text-muted" style="font-size:.8rem;">Preview: </span>
                            <strong id="domain-preview" style="font-size:.85rem; color:#1a1a2e;">.nimvo.com.br</strong>
                        </div>
                    </div>

                    <!-- Nome -->
                    <div class="form-group">
                        <label>Nome da Empresa <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Meu Cliente Ltda"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label>E-mail do Responsavel <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="cliente@exemplo.com"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Comando que sera executado -->
                    <div class="form-group">
                        <label class="text-muted" style="font-size:.8rem;">Comando que sera executado</label>
                        <pre class="output-box mb-0" id="cmd-preview" style="font-size:.78rem; padding:10px;">
php artisan tenant:create <span id="prev-id">meucliente</span> --name="<span id="prev-name">...</span>" --email="<span id="prev-email">...</span>"</pre>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>Voltar
                        </a>
                        <button type="submit" class="btn btn-nimvo">
                            <i class="fas fa-plus-circle mr-1"></i>Criar Tenant
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const idInput    = $('[name="id"]');
    const nameInput  = $('[name="name"]');
    const emailInput = $('[name="email"]');

    function updatePreview() {
        const id    = idInput.val() || 'meucliente';
        const name  = nameInput.val() || '...';
        const email = emailInput.val() || '...';

        $('#domain-preview').text(id + '.nimvo.com.br');
        $('#prev-id').text(id);
        $('#prev-name').text(name);
        $('#prev-email').text(email);
    }

    idInput.on('input', function () {
        // forca minusculo e remove chars invalidos
        const clean = $(this).val().toLowerCase().replace(/[^a-z0-9\-]/g, '');
        $(this).val(clean);
        updatePreview();
    });

    nameInput.on('input', updatePreview);
    emailInput.on('input', updatePreview);
</script>
@endpush
