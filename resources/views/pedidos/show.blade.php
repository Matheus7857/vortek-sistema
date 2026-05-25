@extends('layouts.app')

@section('page-title', 'Pedido #' . $pedido->numero)
@section('page-subtitle', $pedido->nome_cliente)

@section('topbar-actions')
    <a href="{{ route('pedidos.via', $pedido) }}"      target="_blank" class="btn btn-secondary btn-sm no-print">&#128197; 2 Vias</a>
    <a href="{{ route('pedidos.imprimir', $pedido) }}" target="_blank" class="btn btn-secondary btn-sm no-print">&#128438; Layout Produção</a>
    @if($pedido->status === 'rascunho')
        <form method="POST" action="{{ route('pedidos.enviar', $pedido) }}" style="display:inline" class="no-print">
            @csrf
            <button class="btn btn-primary btn-sm">Enviar para Produção</button>
        </form>
    @endif
    @if($pedido->status === 'rascunho' || auth()->user()->temPermissao('pedidos_excluir'))
        <form method="POST" action="{{ route('pedidos.destroy', $pedido) }}" style="display:inline" class="no-print"
              onsubmit="return confirm('Excluir pedido #{{ $pedido->numero }}? Esta ação não pode ser desfeita.')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Excluir Pedido</button>
        </form>
    @endif
@endsection

@section('content')
<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

    {{-- Detalhes principais --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title">Informações do Pedido</span>
                <div style="margin-left:auto;display:flex;gap:8px;align-items:center">
                    <span class="badge badge-{{ $pedido->prioridade }}">{{ \App\Models\Pedido::labelPrioridade($pedido->prioridade) }}</span>
                    <span class="badge badge-{{ $pedido->is_atrasado ? 'atrasado' : $pedido->status }}">
                        {{ $pedido->is_atrasado ? 'Atrasado' : \App\Models\Pedido::labelStatus($pedido->status) }}
                    </span>
                </div>
            </div>
            <div class="modal-body">
                <div class="form-row" style="grid-template-columns:repeat(3,1fr)">
                    <div>
                        <div class="form-label">Cliente</div>
                        <div style="font-weight:500">{{ $pedido->nome_cliente }}</div>
                    </div>
                    <div>
                        <div class="form-label">Faturista</div>
                        <div>{{ $pedido->vendedor->nome }}</div>
                    </div>
                    <div>
                        <div class="form-label">Rota</div>
                        <div>{{ $pedido->rota?->nome ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="form-label">Data de Saída</div>
                        <div style="font-weight:500">{{ $pedido->data_saida->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div class="form-label">Criado por</div>
                        <div>{{ $pedido->criadoPorUser->name }}</div>
                    </div>
                    <div>
                        <div class="form-label">Criado em</div>
                        <div>{{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($pedido->confirmado_em)
                    <div>
                        <div class="form-label">Aceito p/ produção</div>
                        <div>{{ $pedido->confirmadoPorUser?->name ?? 'Painel' }} &middot; <span style="color:var(--text-3);font-size:12px">{{ $pedido->confirmado_em->format('d/m H:i') }}</span></div>
                    </div>
                    @endif
                    @if($pedido->conferido_em)
                    <div>
                        <div class="form-label">Conferido por</div>
                        <div>{{ $pedido->conferidoPorUser?->name ?? '—' }} &middot; <span style="color:var(--text-3);font-size:12px">{{ $pedido->conferido_em->format('d/m H:i') }}</span></div>
                    </div>
                    @endif
                </div>
                @if($pedido->observacoes)
                    <div style="margin-top:12px;padding:10px;background:var(--bg);border-radius:6px;font-size:13px">
                        <div class="form-label">Observações</div>
                        {{ $pedido->observacoes }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Itens --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Itens</span></div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produto</th>
                            <th>Tipo</th>
                            <th>Qtd.</th>
                            <th>Unid.</th>
                            @if($pedido->status === 'conferido')
                                <th>Conf.</th>
                                <th>Qtd. Conf.</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedido->itens as $i => $item)
                            <tr>
                                <td style="color:var(--text-3);font-size:12px">{{ $i + 1 }}</td>
                                <td style="font-weight:500">{{ $item->produto->nome }}</td>
                                <td>{{ $item->tipo === 'fracionado' ? 'Fracionado' : 'Kilo' }}</td>
                                <td style="font-family:'IBM Plex Mono',monospace">{{ number_format($item->quantidade, 3, ',', '.') }}</td>
                                <td>{{ $item->unidade }}</td>
                                @if($pedido->status === 'conferido')
                                    <td>
                                        @if($item->conferencia)
                                            <span style="color:{{ $item->conferencia->conferido ? 'var(--ok)' : 'var(--danger)' }}">
                                                {{ $item->conferencia->conferido ? '✓' : '✗' }}
                                            </span>
                                        @else — @endif
                                    </td>
                                    <td style="font-family:'IBM Plex Mono',monospace;font-size:12px">
                                        {{ $item->conferencia?->quantidade_conferida ? number_format($item->conferencia->quantidade_conferida, 3, ',', '.') : '—' }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Timeline --}}
    <div>
        <div class="card">
            <div class="card-header"><span class="card-title">Histórico</span></div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:12px">
                <div style="display:flex;gap:10px;align-items:flex-start">
                    <div style="width:8px;height:8px;border-radius:50%;background:var(--ok);margin-top:4px;flex-shrink:0"></div>
                    <div>
                        <div style="font-size:12px;font-weight:500">Pedido criado</div>
                        <div style="font-size:11px;color:var(--text-3)">{{ $pedido->created_at->format('d/m/Y H:i') }} — {{ $pedido->criadoPorUser->name }}</div>
                    </div>
                </div>

                @if($pedido->confirmado_em)
                    <div style="display:flex;gap:10px;align-items:flex-start">
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--blue);margin-top:4px;flex-shrink:0"></div>
                        <div>
                            <div style="font-size:12px;font-weight:500">Confirmado pela produção</div>
                            <div style="font-size:11px;color:var(--text-3)">{{ $pedido->confirmado_em->format('d/m/Y H:i') }} — {{ $pedido->confirmadoPorUser?->name }}</div>
                        </div>
                    </div>
                @endif

                @if($pedido->conferido_em)
                    <div style="display:flex;gap:10px;align-items:flex-start">
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--ok);margin-top:4px;flex-shrink:0"></div>
                        <div>
                            <div style="font-size:12px;font-weight:500">Conferência concluída</div>
                            <div style="font-size:11px;color:var(--text-3)">{{ $pedido->conferido_em->format('d/m/Y H:i') }} — {{ $pedido->conferidoPorUser?->name }}</div>
                        </div>
                    </div>
                @endif

                @if($pedido->status === 'pronto')
                    <div style="display:flex;gap:10px;align-items:flex-start">
                        <div style="width:8px;height:8px;border-radius:50%;background:#166534;margin-top:4px;flex-shrink:0"></div>
                        <div>
                            <div style="font-size:12px;font-weight:500">Pronto para entrega</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if($pedido->observacoes_conferencia)
            <div class="card" style="margin-top:12px">
                <div class="card-header"><span class="card-title">Obs. da Conferência</span></div>
                <div style="padding:16px;font-size:13px;color:var(--text-2)">{{ $pedido->observacoes_conferencia }}</div>
            </div>
        @endif

        {{-- Ações de produção --}}
        @if(in_array($pedido->status, ['confirmado', 'producao', 'conferido']) && auth()->user()->isProducao())
            <div class="card no-print" style="margin-top:12px">
                <div class="card-header"><span class="card-title">Ações</span></div>
                <div style="padding:16px;display:flex;flex-direction:column;gap:8px">
                    @if($pedido->status === 'confirmado')
                        <form method="POST" action="{{ route('producao.avancar', $pedido) }}">
                            @csrf
                            <button class="btn btn-primary" style="width:100%">Iniciar Produção</button>
                        </form>
                    @endif
                    @if(in_array($pedido->status, ['producao', 'conferido']))
                        <form method="POST" action="{{ route('producao.pronto', $pedido) }}">
                            @csrf
                            <button class="btn btn-primary" style="width:100%;background:var(--ok)">Marcar como Pronto</button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
