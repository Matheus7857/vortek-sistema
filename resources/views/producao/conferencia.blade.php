@extends('layouts.app')

@section('page-title', 'Conferência')
@section('page-subtitle', 'Verificação de itens')

@section('content')
    @if($pedidos->isEmpty())
        <div class="alert alert-success">Nenhum pedido aguardando conferência.</div>
    @endif

    @foreach($pedidos as $pedido)
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <div>
                    <span style="font-family:'IBM Plex Mono',monospace;font-size:13px;font-weight:600">#{{ $pedido->numero }}</span>
                    <span style="margin-left:8px;font-weight:600">{{ $pedido->nome_cliente }}</span>
                    <span class="badge badge-{{ $pedido->prioridade }}" style="margin-left:8px">{{ \App\Models\Pedido::labelPrioridade($pedido->prioridade) }}</span>
                </div>
                <div class="card-actions" style="font-size:12px;color:var(--text-2)">
                    Saída: {{ $pedido->data_saida->format('d/m/Y') }}
                </div>
            </div>

            <form method="POST" action="{{ route('producao.finalizar-conferencia', $pedido) }}">
                @csrf
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:36px">OK</th>
                                <th>Produto</th>
                                <th>Tipo</th>
                                <th>Qtd. Pedida</th>
                                <th>Qtd. Conferida</th>
                                <th>Obs.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedido->itens as $i => $item)
                                <input type="hidden" name="itens[{{ $i }}][item_id]" value="{{ $item->id }}">
                                <tr>
                                    <td>
                                        <input type="checkbox"
                                               name="itens[{{ $i }}][conferido]"
                                               value="1"
                                               @checked($item->conferencia?->conferido)
                                               style="width:16px;height:16px;cursor:pointer">
                                    </td>
                                    <td style="font-weight:500">{{ $item->produto->nome }}</td>
                                    <td>{{ $item->tipo === 'fracionado' ? 'Fracionado' : 'Kilo' }}</td>
                                    <td style="font-family:'IBM Plex Mono',monospace">
                                        {{ number_format($item->quantidade, 3, ',', '.') }} {{ $item->unidade }}
                                    </td>
                                    <td>
                                        <input type="number"
                                               name="itens[{{ $i }}][quantidade_conferida]"
                                               class="form-control"
                                               style="width:100px"
                                               step="0.001" min="0"
                                               value="{{ $item->conferencia?->quantidade_conferida ?? $item->quantidade }}">
                                    </td>
                                    <td>
                                        <input type="text"
                                               name="itens[{{ $i }}][observacoes]"
                                               class="form-control"
                                               placeholder="Opcional"
                                               value="{{ $item->conferencia?->observacoes }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="padding:14px 16px;border-top:1px solid var(--border);display:flex;gap:12px;align-items:flex-end">
                    <div class="form-group" style="flex:1;margin:0">
                        <label class="form-label">Observações gerais da conferência</label>
                        <input type="text" name="observacoes_gerais" class="form-control" value="{{ $pedido->observacoes_conferencia }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Finalizar Conferência</button>
                </div>
            </form>
        </div>
    @endforeach
@endsection
