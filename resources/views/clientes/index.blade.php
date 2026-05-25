@extends('layouts.app')

@section('page-title', 'Clientes')

@section('topbar-actions')
    <button class="btn btn-primary btn-sm no-print" onclick="document.getElementById('modal-cliente').classList.add('open')">+ Novo Cliente</button>
@endsection

@section('content')
    <div class="metric-grid" style="grid-template-columns:repeat(2,160px)">
        <div class="metric-card"><div class="metric-label">Total</div><div class="metric-value">{{ $metricas['total'] }}</div></div>
        <div class="metric-card ok"><div class="metric-label">Ativos</div><div class="metric-value">{{ $metricas['ativos'] }}</div></div>
    </div>

    <div class="card" style="margin-bottom:12px">
        <div class="card-header" style="padding:10px 16px">
            <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;width:100%;align-items:center">
                <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar..." class="form-control" style="width:200px">
                <select name="rota" class="form-control" style="width:140px">
                    <option value="">Todas as rotas</option>
                    @foreach($rotas as $rota)
                        <option value="{{ $rota->id }}" @selected(request('rota') == $rota->id)>{{ $rota->codigo }}</option>
                    @endforeach
                </select>
                <select name="ativo" class="form-control" style="width:120px">
                    <option value="">Todos</option>
                    <option value="1" @selected(request('ativo')==='1')>Ativos</option>
                    <option value="0" @selected(request('ativo')==='0')>Inativos</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Nome</th><th>Contato</th><th>Telefone</th><th>Rota</th><th>Cidade</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($clientes as $c)
                        <tr>
                            <td style="font-weight:500">{{ $c->nome }}</td>
                            <td>{{ $c->contato ?? '—' }}</td>
                            <td>{{ $c->telefone ?? '—' }}</td>
                            <td>{{ $c->rota?->codigo ?? '—' }}</td>
                            <td>{{ $c->cidade ?? '—' }}</td>
                            <td>
                                <span class="badge" style="{{ $c->ativo ? 'background:var(--ok-light);color:var(--ok)' : 'background:#f1f0ed;color:var(--text-3)' }}">
                                    {{ $c->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td style="white-space:nowrap">
                                <button class="btn btn-secondary btn-sm"
                                    onclick='openEditCliente(@json($c))'>Editar</button>
                                <form method="POST" action="{{ route('clientes.toggle', $c) }}" style="display:inline">
                                    @csrf
                                    <button class="btn btn-secondary btn-sm">{{ $c->ativo ? 'Desativar' : 'Ativar' }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:var(--text-3);padding:24px">Nenhum cliente encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $clientes->links() }}

    {{-- Modal Novo/Editar Cliente --}}
    <div class="modal-backdrop" id="modal-cliente" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title" id="modal-cliente-title">Novo Cliente</span>
                <button class="modal-close" onclick="document.getElementById('modal-cliente').classList.remove('open')">&times;</button>
            </div>
            <form method="POST" id="form-cliente" action="{{ route('clientes.store') }}">
                @csrf
                <input type="hidden" name="_method" id="cliente-method" value="POST">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="nome" id="c-nome" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Contato</label>
                            <input type="text" name="contato" id="c-contato" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone" id="c-telefone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" id="c-email" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">CNPJ/CPF</label>
                            <input type="text" name="cpf_cnpj" id="c-cpf_cnpj" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rota</label>
                            <select name="rota_id" id="c-rota_id" class="form-control">
                                <option value="">Sem rota</option>
                                @foreach($rotas as $r)
                                    <option value="{{ $r->id }}">{{ $r->codigo }} — {{ $r->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="cidade" id="c-cidade" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Endereço</label>
                        <input type="text" name="endereco" id="c-endereco" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" id="c-observacoes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-cliente').classList.remove('open')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
function openEditCliente(c) {
    const modal = document.getElementById('modal-cliente');
    document.getElementById('modal-cliente-title').textContent = 'Editar Cliente';
    document.getElementById('form-cliente').action = `/cadastros/clientes/${c.id}`;
    document.getElementById('cliente-method').value = 'PUT';
    ['nome','contato','telefone','email','cpf_cnpj','cidade','endereco','observacoes'].forEach(f => {
        const el = document.getElementById('c-' + f);
        if (el) el.value = c[f] ?? '';
    });
    document.getElementById('c-rota_id').value = c.rota_id ?? '';
    modal.classList.add('open');
}
</script>
@endpush
@endsection
