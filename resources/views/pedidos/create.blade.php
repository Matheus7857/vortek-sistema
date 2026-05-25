@extends('layouts.app')

@section('page-title', 'Novo Pedido')

@section('content')
<form method="POST" action="{{ route('pedidos.store') }}" id="form-pedido">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

        {{-- Coluna principal --}}
        <div>
            <div class="card">
                <div class="card-header"><span class="card-title">Dados do Pedido</span></div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Cliente *</label>
                            <input type="text" name="cliente_nome" class="form-control"
                                   value="{{ old('cliente_nome') }}"
                                   placeholder="Digite o nome do cliente..."
                                   required autocomplete="off">
                            @error('cliente_nome')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Faturista *</label>
                            <select name="vendedor_id" class="form-control" required>
                                <option value="">Selecione...</option>
                                @foreach($vendedores as $v)
                                    <option value="{{ $v->id }}" @selected(old('vendedor_id') == $v->id)>{{ $v->nome }}</option>
                                @endforeach
                            </select>
                            @error('vendedor_id')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Rota</label>
                            <select name="rota_id" class="form-control">
                                <option value="">Sem rota</option>
                                @foreach($rotas as $r)
                                    <option value="{{ $r->id }}" @selected(old('rota_id') == $r->id)>{{ $r->codigo }} — {{ $r->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Data de Saída *</label>
                            <input type="date" name="data_saida" class="form-control" value="{{ old('data_saida', date('Y-m-d')) }}" required>
                            @error('data_saida')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Prioridade *</label>
                            <select name="prioridade" class="form-control" required>
                                <option value="normal"  @selected(old('prioridade','normal')=='normal') >Normal</option>
                                <option value="alta"    @selected(old('prioridade')=='alta')  >Alta</option>
                                <option value="urgente" @selected(old('prioridade')=='urgente')>Urgente</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-control" rows="2">{{ old('observacoes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Itens --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Itens do Pedido</span>
                    <div class="card-actions">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addItem()">+ Adicionar Item</button>
                    </div>
                </div>
                <div id="itens-container" style="padding:0 16px 16px">
                    @if(old('itens'))
                        @foreach(old('itens') as $i => $item)
                            <div class="item-row" style="display:grid;grid-template-columns:1fr 120px 90px 80px 32px;gap:8px;margin-top:12px;align-items:end">
                                <div>
                                    <label class="form-label">Produto</label>
                                    <select name="itens[{{ $i }}][produto_id]" class="form-control" required>
                                        <option value="">Selecione...</option>
                                        @foreach($produtos as $p)
                                            <option value="{{ $p->id }}" @selected($item['produto_id'] == $p->id)>{{ $p->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Tipo</label>
                                    <select name="itens[{{ $i }}][tipo]" class="form-control">
                                        <option value="fracionado" @selected($item['tipo']=='fracionado')>Frac.</option>
                                        <option value="kilo"       @selected($item['tipo']=='kilo')>Kilo</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Qtd.</label>
                                    <input type="number" name="itens[{{ $i }}][quantidade]" class="form-control" value="{{ $item['quantidade'] }}" step="0.001" min="0.001" required>
                                </div>
                                <div>
                                    <label class="form-label">Unid.</label>
                                    <select name="itens[{{ $i }}][unidade]" class="form-control">
                                        <option value="kg"  @selected($item['unidade']=='kg') >kg</option>
                                        <option value="g"   @selected($item['unidade']=='g')  >g</option>
                                        <option value="un"  @selected($item['unidade']=='un') >un</option>
                                        <option value="pct" @selected($item['unidade']=='pct')>pct</option>
                                    </select>
                                </div>
                                <div style="padding-top:20px">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)" title="Remover">&times;</button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div id="no-items" style="text-align:center;padding:20px;color:var(--text-3);font-size:13px @if(old('itens')) ;display:none @endif">
                        Nenhum item adicionado. Clique em "+ Adicionar Item".
                    </div>
                </div>
                @error('itens')<div style="padding:0 16px 12px;color:var(--danger);font-size:12px">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Sidebar de ações --}}
        <div>
            <div class="card">
                <div class="card-header"><span class="card-title">Ações</span></div>
                <div class="modal-body" style="display:flex;flex-direction:column;gap:8px">
                    <button type="submit" class="btn btn-primary" style="width:100%">
                        Enviar para Produção
                    </button>
                    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary" style="width:100%;text-align:center">
                        Cancelar
                    </a>
                </div>
            </div>

            <div class="card" style="margin-top:12px">
                <div class="card-header"><span class="card-title">Produtos Disponíveis</span></div>
                <div style="padding:12px 16px;max-height:300px;overflow-y:auto">
                    @foreach($produtos->groupBy('categoria') as $cat => $prods)
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--text-3);letter-spacing:.06em;margin:8px 0 4px">{{ ucfirst($cat) }}</div>
                        @foreach($prods as $p)
                            <div style="font-size:12px;padding:3px 0;color:var(--text-2)">{{ $p->nome }}</div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
@php
    $produtosJs = $produtos->map(fn($p) => [
        'id'      => $p->id,
        'nome'    => $p->nome,
        'tipo'    => $p->tipo_padrao,
        'unidade' => $p->unidade,
    ]);
@endphp
<script>
    let itemIndex = {{ old('itens') ? count(old('itens')) : 0 }};

    const produtos = @json($produtosJs);

    function buildOptions() {
        return produtos.map(p => `<option value="${p.id}">${p.nome}</option>`).join('');
    }

    function addItem() {
        document.getElementById('no-items').style.display = 'none';
        const row = document.createElement('div');
        row.className = 'item-row';
        row.style.cssText = 'display:grid;grid-template-columns:1fr 120px 90px 80px 32px;gap:8px;margin-top:12px;align-items:end';
        row.innerHTML = `
            <div>
                <label class="form-label">Produto</label>
                <select name="itens[${itemIndex}][produto_id]" class="form-control" required onchange="onProdutoChange(this, ${itemIndex})">
                    <option value="">Selecione...</option>
                    ${buildOptions()}
                </select>
            </div>
            <div>
                <label class="form-label">Tipo</label>
                <select name="itens[${itemIndex}][tipo]" id="tipo-${itemIndex}" class="form-control">
                    <option value="fracionado">Frac.</option>
                    <option value="kilo">Kilo</option>
                </select>
            </div>
            <div>
                <label class="form-label">Qtd.</label>
                <input type="number" name="itens[${itemIndex}][quantidade]" class="form-control" value="1" step="0.001" min="0.001" required>
            </div>
            <div>
                <label class="form-label">Unid.</label>
                <select name="itens[${itemIndex}][unidade]" id="unid-${itemIndex}" class="form-control">
                    <option value="kg">kg</option>
                    <option value="g">g</option>
                    <option value="un">un</option>
                    <option value="pct">pct</option>
                </select>
            </div>
            <div style="padding-top:20px">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)" title="Remover">&times;</button>
            </div>`;
        document.getElementById('itens-container').appendChild(row);
        itemIndex++;
    }

    function onProdutoChange(sel, idx) {
        const p = produtos.find(x => x.id == sel.value);
        if (!p) return;
        const tipoEl = document.getElementById('tipo-' + idx);
        const unidEl = document.getElementById('unid-' + idx);
        if (tipoEl) tipoEl.value = p.tipo;
        if (unidEl) unidEl.value = p.unidade;
    }

    function removeItem(btn) {
        btn.closest('.item-row').remove();
        const rows = document.querySelectorAll('.item-row');
        document.getElementById('no-items').style.display = rows.length ? 'none' : 'block';
    }

    // Botão "Salvar e Enviar" altera o status para enviado via campo hidden
    document.getElementById('form-pedido').addEventListener('submit', function(e) {
        const action = document.activeElement?.value;
        if (action === 'enviar') {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = '_enviar'; inp.value = '1';
            this.appendChild(inp);
        }
    });
</script>
@endpush
@endsection
