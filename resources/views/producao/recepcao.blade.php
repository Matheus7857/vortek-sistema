@extends('layouts.app')

@section('page-title', 'Receber Pedidos')
@section('page-subtitle', 'Confirmação pela produção')

@section('content')
    @if($pedidos->isEmpty())
        <div class="alert alert-success">Nenhum pedido aguardando confirmação.</div>
    @else
        <div class="alert alert-warn">
            &#9888; {{ $pedidos->count() }} pedido(s) aguardando confirmação.
        </div>
    @endif

    @foreach($pedidos as $pedido)
        <div class="card" style="margin-bottom:16px;border-left:3px solid {{ $pedido->prioridade === 'urgente' ? 'var(--danger)' : ($pedido->prioridade === 'alta' ? '#f59e0b' : 'var(--border)') }}">
            <div class="card-header">
                <div>
                    <span style="font-family:'IBM Plex Mono',monospace;font-size:13px;font-weight:600">#{{ $pedido->numero }}</span>
                    <span style="margin-left:8px;font-weight:600">{{ $pedido->nome_cliente }}</span>
                    <span class="badge badge-{{ $pedido->prioridade }}" style="margin-left:8px">{{ \App\Models\Pedido::labelPrioridade($pedido->prioridade) }}</span>
                </div>
                <div class="card-actions">
                    <span style="font-size:12px;color:var(--text-2)">Saída: {{ $pedido->data_saida->format('d/m/Y') }}</span>
                    @if($pedido->rota)
                        <span class="badge badge-normal">{{ $pedido->rota->codigo }}</span>
                    @endif
                    <a href="{{ route('pedidos.imprimir', $pedido) }}" target="_blank" class="btn btn-secondary btn-sm">&#128438; Imprimir</a>
                    <form method="POST" action="{{ route('producao.confirmar', $pedido) }}">
                        @csrf
                        <button class="btn btn-primary btn-sm">Confirmar Recebimento</button>
                    </form>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>#</th><th>Produto</th><th>Tipo</th><th>Quantidade</th><th>Unidade</th></tr>
                    </thead>
                    <tbody>
                        @foreach($pedido->itens as $i => $item)
                            <tr>
                                <td style="color:var(--text-3);font-size:12px">{{ $i + 1 }}</td>
                                <td style="font-weight:500">{{ $item->produto->nome }}</td>
                                <td>{{ $item->tipo === 'fracionado' ? 'Fracionado' : 'Kilo' }}</td>
                                <td style="font-family:'IBM Plex Mono',monospace">{{ number_format($item->quantidade, 3, ',', '.') }}</td>
                                <td>{{ $item->unidade }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($pedido->observacoes)
                <div style="padding:10px 16px;font-size:12px;color:var(--text-2);background:var(--bg);border-top:1px solid var(--border)">
                    Obs: {{ $pedido->observacoes }}
                </div>
            @endif
        </div>
    @endforeach
@endsection
