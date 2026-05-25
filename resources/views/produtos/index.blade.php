@extends('layouts.app')

@section('page-title', 'Produtos')

@section('topbar-actions')
    <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-produto').classList.add('open')">+ Novo Produto</button>
@endsection

@section('content')
    <div class="metric-grid" style="grid-template-columns:repeat(4,160px)">
        <div class="metric-card"><div class="metric-label">Total</div><div class="metric-value">{{ $metricas['total'] }}</div></div>
        <div class="metric-card ok"><div class="metric-label">Ativos</div><div class="metric-value">{{ $metricas['ativos'] }}</div></div>
        <div class="metric-card"><div class="metric-label">Embutidos</div><div class="metric-value">{{ $metricas['embutidos'] }}</div></div>
        <div class="metric-card"><div class="metric-label">Queijos</div><div class="metric-value">{{ $metricas['queijos'] }}</div></div>
    </div>

    <div class="card" style="margin-bottom:12px">
        <div class="card-header" style="padding:10px 16px">
            <form method="GET" style="display:flex;gap:10px;align-items:center">
                <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar..." class="form-control" style="width:200px">
                <select name="categoria" class="form-control" style="width:150px">
                    <option value="">Todas categorias</option>
                    <option value="embutido" @selected(request('categoria')==='embutido')>Embutido</option>
                    <option value="queijo"   @selected(request('categoria')==='queijo')  >Queijo</option>
                    <option value="outro"    @selected(request('categoria')==='outro')   >Outro</option>
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
                    <tr><th>Nome</th><th>Categoria</th><th>Tipo Padrão</th><th>Unidade</th><th>Peso Mín.</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($produtos as $p)
                        <tr>
                            <td style="font-weight:500">{{ $p->nome }}</td>
                            <td>{{ ucfirst($p->categoria) }}</td>
                            <td>{{ $p->tipo_padrao === 'fracionado' ? 'Fracionado' : 'Kilo' }}</td>
                            <td>{{ $p->unidade }}</td>
                            <td>{{ $p->peso_minimo ? number_format($p->peso_minimo,3,',','.') . ' ' . $p->unidade : '—' }}</td>
                            <td>
                                <span class="badge" style="{{ $p->ativo ? 'background:var(--ok-light);color:var(--ok)' : 'background:#f1f0ed;color:var(--text-3)' }}">
                                    {{ $p->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td style="white-space:nowrap">
                                <button class="btn btn-secondary btn-sm" onclick='openEditProduto(@json($p))'>Editar</button>
                                <form method="POST" action="{{ route('produtos.toggle', $p) }}" style="display:inline">
                                    @csrf
                                    <button class="btn btn-secondary btn-sm">{{ $p->ativo ? 'Desativar' : 'Ativar' }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:var(--text-3);padding:24px">Nenhum produto encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $produtos->links() }}

    {{-- Modal --}}
    <div class="modal-backdrop" id="modal-produto" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title" id="modal-produto-title">Novo Produto</span>
                <button class="modal-close" onclick="document.getElementById('modal-produto').classList.remove('open')">&times;</button>
            </div>
            <form method="POST" id="form-produto" action="{{ route('produtos.store') }}">
                @csrf
                <input type="hidden" name="_method" id="produto-method" value="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" id="p-nome" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Categoria *</label>
                            <select name="categoria" id="p-categoria" class="form-control" required>
                                <option value="embutido">Embutido</option>
                                <option value="queijo">Queijo</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tipo Padrão *</label>
                            <select name="tipo_padrao" id="p-tipo_padrao" class="form-control" required>
                                <option value="fracionado">Fracionado</option>
                                <option value="kilo">Kilo</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Unidade *</label>
                            <select name="unidade" id="p-unidade" class="form-control" required>
                                <option value="kg">kg</option>
                                <option value="g">g</option>
                                <option value="un">un</option>
                                <option value="pct">pct</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Peso Mínimo (frac.)</label>
                            <input type="number" name="peso_minimo" id="p-peso_minimo" class="form-control" step="0.001" min="0" placeholder="0.000">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" id="p-observacoes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-produto').classList.remove('open')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
function openEditProduto(p) {
    document.getElementById('modal-produto-title').textContent = 'Editar Produto';
    document.getElementById('form-produto').action = `/cadastros/produtos/${p.id}`;
    document.getElementById('produto-method').value = 'PUT';
    ['nome','categoria','tipo_padrao','unidade','observacoes'].forEach(f => {
        const el = document.getElementById('p-' + f);
        if (el) el.value = p[f] ?? '';
    });
    document.getElementById('p-peso_minimo').value = p.peso_minimo ?? '';
    document.getElementById('modal-produto').classList.add('open');
}
</script>
@endpush
@endsection
