@extends('layouts.app')

@section('page-title', 'Calendário de Rotas')

@section('content')
    @if($pedidos->isEmpty())
        <div class="alert alert-success">Nenhum pedido ativo no calendário.</div>
    @endif

    @foreach($pedidos as $data => $grupo)
        @php
            $dt     = \Carbon\Carbon::parse($data);
            $isHoje = $dt->isToday();
            $isPast = $dt->isPast() && ! $isHoje;
        @endphp
        <div style="margin-bottom:24px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                <h3 style="font-size:15px;font-weight:600">{{ $dt->translatedFormat('l, d \d\e F') }}</h3>
                @if($isHoje)
                    <span class="badge badge-ok" style="background:var(--ok-light);color:var(--ok)">Hoje</span>
                @elseif($isPast)
                    <span class="badge badge-atrasado">Atrasado</span>
                @endif
                <span style="font-size:12px;color:var(--text-3)">{{ $grupo->count() }} pedido(s)</span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px">
                @foreach($grupo as $pedido)
                    <div class="card" style="margin:0;border-left:3px solid {{ $pedido->prioridade === 'urgente' ? 'var(--danger)' : ($pedido->prioridade === 'alta' ? '#f59e0b' : 'var(--border)') }}">
                        <div style="padding:12px 14px">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                                <div>
                                    <span style="font-family:'IBM Plex Mono',monospace;font-size:11px;color:var(--text-3)">#{{ $pedido->numero }}</span>
                                    <div style="font-weight:600;margin-top:2px">{{ $pedido->nome_cliente }}</div>
                                </div>
                                <div style="text-align:right">
                                    @if($pedido->rota)
                                        <span class="badge badge-normal">{{ $pedido->rota->codigo }}</span>
                                    @endif
                                    <div style="margin-top:4px">
                                        <span class="badge badge-{{ $pedido->status }}">{{ \App\Models\Pedido::labelStatus($pedido->status) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div style="font-size:12px;color:var(--text-2)">{{ $pedido->itens->count() }} iten(s)</div>
                            <div style="margin-top:8px">
                                <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-secondary btn-sm">Ver pedido</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endsection
