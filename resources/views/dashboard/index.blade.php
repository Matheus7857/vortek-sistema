@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral')

@section('topbar-actions')
    <a href="{{ route('pedidos.create') }}" class="btn btn-primary btn-sm no-print">+ Novo Pedido</a>
@endsection

@section('content')
    <div class="metric-grid">
        <div class="metric-card">
            <div class="metric-label">Total de Pedidos</div>
            <div class="metric-value">{{ $metricas['total'] }}</div>
        </div>
        <div class="metric-card warn">
            <div class="metric-label">Aguard. Confirmação</div>
            <div class="metric-value">{{ $metricas['nao_confirmados'] }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Em Produção</div>
            <div class="metric-value">{{ $metricas['em_producao'] }}</div>
        </div>
        <div class="metric-card danger">
            <div class="metric-label">Atrasados</div>
            <div class="metric-value">{{ $metricas['atrasados'] }}</div>
        </div>
        <div class="metric-card ok">
            <div class="metric-label">Prontos Hoje</div>
            <div class="metric-value">{{ $metricas['prontos_hoje'] }}</div>
        </div>
    </div>

    @if($metricas['nao_confirmados'] > 0)
        <div class="alert alert-warn">
            &#9888; {{ $metricas['nao_confirmados'] }} pedido(s) aguardando confirmação da produção.
            <a href="{{ route('producao.recepcao') }}" style="margin-left:8px;font-weight:600;color:inherit">Ver agora &rarr;</a>
        </div>
    @endif

    @if($metricas['atrasados'] > 0)
        <div class="alert alert-danger">
            &#9888; {{ $metricas['atrasados'] }} pedido(s) com data de saída vencida.
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <span class="card-title">Pedidos Recentes</span>
            <div class="card-actions">
                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary btn-sm">Ver todos</a>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Rota</th>
                        <th>Saída</th>
                        <th>Status</th>
                        <th>Prioridade</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidos_recentes as $pedido)
                        <tr>
                            <td><span style="font-family:'IBM Plex Mono',monospace;font-size:12px">#{{ $pedido->numero }}</span></td>
                            <td>{{ $pedido->nome_cliente }}</td>
                            <td>{{ $pedido->rota?->codigo ?? '—' }}</td>
                            <td>
                                {{ $pedido->data_saida->format('d/m/Y') }}
                                @if($pedido->is_atrasado)
                                    <span class="badge badge-atrasado" style="margin-left:4px">Atrasado</span>
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $pedido->status }}">{{ \App\Models\Pedido::labelStatus($pedido->status) }}</span></td>
                            <td><span class="badge badge-{{ $pedido->prioridade }}">{{ \App\Models\Pedido::labelPrioridade($pedido->prioridade) }}</span></td>
                            <td><a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-secondary btn-sm">Ver</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:var(--text-3);padding:24px">Nenhum pedido ainda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
