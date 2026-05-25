@extends('layouts.app')

@section('page-title', 'Painel KDS')
@section('page-subtitle', 'Kitchen Display System')

@section('topbar-actions')
    <button onclick="location.reload()" class="btn btn-secondary btn-sm no-print">&#8635; Atualizar</button>
@endsection

@section('content')
<div class="kds-grid">

    {{-- Pendentes --}}
    <div>
        <div class="kds-col-header" style="color:#991b1b;border-bottom-color:#fecaca">
            Pendentes
            <span style="font-weight:400;margin-left:4px">({{ $agrupados['pendente']->count() }})</span>
            @if($agrupados['pendente']->count())
                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#dc2626;margin-left:6px;animation:pulse 1.2s infinite"></span>
            @endif
        </div>
        @forelse($agrupados['pendente'] as $pedido)
            <div class="kds-card {{ $pedido->prioridade }}" style="border-left-color:{{ $pedido->prioridade === 'urgente' ? '#dc2626' : ($pedido->prioridade === 'alta' ? '#f59e0b' : 'var(--border)') }}">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
                    <div>
                        <span style="font-family:'IBM Plex Mono',monospace;font-size:12px;color:var(--text-3)">#{{ $pedido->numero }}</span>
                        <div style="font-weight:700;margin-top:2px;font-size:14px">{{ $pedido->nome_cliente }}</div>
                    </div>
                    <div style="text-align:right">
                        <span class="badge badge-{{ $pedido->prioridade }}">{{ \App\Models\Pedido::labelPrioridade($pedido->prioridade) }}</span>
                        <div style="font-size:11px;color:var(--text-3);margin-top:3px">{{ $pedido->data_saida->format('d/m') }}</div>
                    </div>
                </div>

                <ul style="list-style:none;font-size:12px;margin-bottom:10px">
                    @foreach($pedido->itens as $item)
                        <li style="padding:4px 0;border-bottom:1px solid var(--border);display:flex;justify-content:space-between">
                            <span style="font-weight:500">{{ $item->produto->nome }}</span>
                            <span style="font-family:'IBM Plex Mono',monospace;color:var(--text-2)">
                                {{ number_format($item->quantidade,3,',','.') }} {{ $item->unidade }}
                            </span>
                        </li>
                    @endforeach
                </ul>

                {{-- Botão: aceita + abre 2 vias --}}
                <form id="form-aceitar-{{ $pedido->id }}"
                      method="POST"
                      action="{{ route('producao.aceitar', $pedido) }}">
                    @csrf
                    <button type="button"
                            class="btn btn-primary btn-sm"
                            style="width:100%;background:#dc2626;font-size:13px;padding:9px"
                            onclick="aceitarPedido({{ $pedido->id }}, '{{ route('pedidos.via', $pedido) }}')">
                        &#9654; Aceitar + Imprimir 2 Vias
                    </button>
                </form>
                <a href="{{ route('pedidos.cupom', $pedido) }}" target="_blank"
                   class="btn btn-secondary btn-sm" style="width:100%;margin-top:4px;text-align:center;display:block;font-size:11px">
                    &#128438; Cupom térmico (80mm)
                </a>
            </div>
        @empty
            <div style="text-align:center;padding:28px 20px;color:var(--text-3);font-size:13px">
                <div style="font-size:24px;margin-bottom:6px">&#10003;</div>
                Sem pendências
            </div>
        @endforelse
    </div>

    {{-- Em produção --}}
    <div>
        <div class="kds-col-header" style="color:var(--warn)">
            Em Produção <span style="font-weight:400;margin-left:4px">({{ $agrupados['producao']->count() }})</span>
        </div>
        @forelse($agrupados['producao'] as $pedido)
            <div class="kds-card {{ $pedido->prioridade }}">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
                    <div>
                        <span style="font-family:'IBM Plex Mono',monospace;font-size:12px;color:var(--text-3)">#{{ $pedido->numero }}</span>
                        <div style="font-weight:600;margin-top:2px">{{ $pedido->nome_cliente }}</div>
                    </div>
                    <div style="text-align:right">
                        <span class="badge badge-{{ $pedido->prioridade }}">{{ \App\Models\Pedido::labelPrioridade($pedido->prioridade) }}</span>
                        <div style="font-size:11px;color:var(--text-3);margin-top:3px">{{ $pedido->data_saida->format('d/m') }}</div>
                    </div>
                </div>
                <ul style="list-style:none;font-size:12px;margin-bottom:10px">
                    @foreach($pedido->itens as $item)
                        <li style="padding:3px 0;border-bottom:1px solid var(--border);display:flex;justify-content:space-between">
                            <span>{{ $item->produto->nome }}</span>
                            <span style="font-family:'IBM Plex Mono',monospace;color:var(--text-2)">
                                {{ number_format($item->quantidade,3,',','.') }} {{ $item->unidade }}
                            </span>
                        </li>
                    @endforeach
                </ul>
                @if($pedido->confirmadoPorUser)
                    <div style="font-size:11px;color:var(--text-3);margin-bottom:8px">
                        &#128100; {{ $pedido->confirmadoPorUser->name }}
                        @if($pedido->confirmado_em) &middot; {{ $pedido->confirmado_em->format('H:i') }} @endif
                    </div>
                @endif
                <div style="display:flex;gap:6px">
                    <a href="{{ route('pedidos.via', $pedido) }}" target="_blank"
                       class="btn btn-secondary btn-sm" style="flex:1;justify-content:center">
                        &#128197; 2 Vias
                    </a>
                    <form method="POST" action="{{ route('producao.pronto', $pedido) }}" style="flex:2">
                        @csrf
                        <button class="btn btn-primary btn-sm" style="width:100%;background:var(--ok)">
                            &#10003; Pronto
                        </button>
                    </form>
                </div>
                <a href="{{ route('pedidos.cupom', $pedido) }}" target="_blank"
                   class="btn btn-secondary btn-sm" style="width:100%;margin-top:4px;text-align:center;display:block;font-size:11px">
                    &#128438; Cupom térmico (80mm)
                </a>
                <form method="POST" action="{{ route('painel.voltar-pendente', $pedido) }}" style="margin-top:6px">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;color:var(--text-3);font-size:11px">
                        &#8592; Voltar para Pendente
                    </button>
                </form>
            </div>
        @empty
            <div style="text-align:center;padding:28px 20px;color:var(--text-3);font-size:13px">Nenhum pedido</div>
        @endforelse
    </div>

    {{-- Prontos --}}
    <div>
        <div class="kds-col-header" style="color:var(--ok)">
            Prontos <span style="font-weight:400;margin-left:4px">({{ $agrupados['pronto']->count() }})</span>
        </div>
        @forelse($agrupados['pronto'] as $pedido)
            <div class="kds-card" style="opacity:.8">
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div>
                        <span style="font-family:'IBM Plex Mono',monospace;font-size:12px;color:var(--text-3)">#{{ $pedido->numero }}</span>
                        <div style="font-weight:600;margin-top:2px">{{ $pedido->nome_cliente }}</div>
                    </div>
                    <div style="text-align:right">
                        <span class="badge badge-pronto">Pronto</span>
                        <div style="font-size:11px;color:var(--text-3);margin-top:3px">{{ $pedido->data_saida->format('d/m') }}</div>
                    </div>
                </div>
                <div style="font-size:12px;color:var(--text-3);margin-top:8px;margin-bottom:8px">{{ $pedido->itens->count() }} iten(s)</div>
                <form method="POST" action="{{ route('painel.voltar-producao', $pedido) }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;color:var(--text-3);font-size:11px">
                        &#8592; Voltar para Produção
                    </button>
                </form>
            </div>
        @empty
            <div style="text-align:center;padding:28px 20px;color:var(--text-3);font-size:13px">Nenhum pedido</div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: .3; }
}
</style>
@endpush

@push('scripts')
<script>
    function aceitarPedido(id, cupomUrl) {
        window.open(cupomUrl, '_blank');
        setTimeout(() => document.getElementById('form-aceitar-' + id).submit(), 300);
    }

    // Auto-atualiza o KDS a cada 30 segundos
    setTimeout(() => location.reload(), 30000);
</script>
@endpush
@endsection
