@extends('layouts.app')

@section('page-title', 'Operadores')

@section('topbar-actions')
    <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-vendedor').classList.add('open')">+ Novo Operador</button>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Nome</th><th>Setor</th><th>Telefone</th><th>E-mail</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($vendedores as $v)
                        <tr>
                            <td style="font-weight:500">{{ $v->nome }}</td>
                            <td>{{ ucfirst($v->setor) }}</td>
                            <td>{{ $v->telefone ?? '—' }}</td>
                            <td>{{ $v->email ?? '—' }}</td>
                            <td>
                                <span class="badge" style="{{ $v->ativo ? 'background:var(--ok-light);color:var(--ok)' : 'background:#f1f0ed;color:var(--text-3)' }}">
                                    {{ $v->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td style="white-space:nowrap">
                                <button class="btn btn-secondary btn-sm" onclick='openEditVendedor(@json($v))'>Editar</button>
                                <form method="POST" action="{{ route('vendedores.toggle', $v) }}" style="display:inline">
                                    @csrf
                                    <button class="btn btn-secondary btn-sm">{{ $v->ativo ? 'Desativar' : 'Ativar' }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:var(--text-3);padding:24px">Nenhum operador cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-backdrop" id="modal-vendedor" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title" id="modal-vendedor-title">Novo Operador</span>
                <button class="modal-close" onclick="document.getElementById('modal-vendedor').classList.remove('open')">&times;</button>
            </div>
            <form method="POST" id="form-vendedor" action="{{ route('vendedores.store') }}">
                @csrf
                <input type="hidden" name="_method" id="vendedor-method" value="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" id="v-nome" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Setor *</label>
                            <select name="setor" id="v-setor" class="form-control" required>
                                <option value="faturamento">Faturamento</option>
                                <option value="vendas">Vendas</option>
                                <option value="producao">Produção</option>
                                <option value="administrativo">Administrativo</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone" id="v-telefone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" id="v-email" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-vendedor').classList.remove('open')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
function openEditVendedor(v) {
    document.getElementById('modal-vendedor-title').textContent = 'Editar Operador';
    document.getElementById('form-vendedor').action = `/cadastros/vendedores/${v.id}`;
    document.getElementById('vendedor-method').value = 'PUT';
    ['nome','setor','telefone','email'].forEach(f => {
        const el = document.getElementById('v-' + f);
        if (el) el.value = v[f] ?? '';
    });
    document.getElementById('modal-vendedor').classList.add('open');
}
</script>
@endpush
@endsection
