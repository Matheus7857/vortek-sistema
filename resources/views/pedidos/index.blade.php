@extends('layouts.app')

@section('page-title', 'Pedidos')

@section('topbar-actions')
    <a href="{{ route('pedidos.create') }}" class="btn btn-primary btn-sm no-print">+ Novo Pedido</a>
@endsection

@section('content')
    {{-- Filtros --}}
    <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="padding:12px 16px">
            <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;width:100%;align-items:center">
                <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar cliente..." class="form-control" style="width:200px">
                <select name="status" class="form-control" style="width:160px">
                    <option value="">Todos os status</option>
                    @foreach(['rascunho','enviado','confirmado','producao','conferido','pronto'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ \App\Models\Pedido::labelStatus($s) }}</option>
                    @endforeach
                </select>
                <select name="rota" class="form-control" style="width:140px">
                    <option value="">Todas as rotas</option>
                    @foreach($rotas as $rota)
                        <option value="{{ $rota->id }}" @selected(request('rota') == $rota->id)>{{ $rota->codigo }}</option>
                    @endforeach
                </select>
                <select name="prioridade" class="form-control" style="width:140px">
                    <option value="">Prioridade</option>
                    <option value="urgente" @selected(request('prioridade')=='urgente')>Urgente</option>
                    <option value="alta"    @selected(request('prioridade')=='alta')>Alta</option>
                    <option value="normal"  @selected(request('prioridade')=='normal')>Normal</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button>
                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary btn-sm">Limpar</a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Rota</th>
                        <th>Saída</th>
                        <th>Prioridade</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidos as $pedido)
                        <tr>
                            <td><span style="font-family:'IBM Plex Mono',monospace;font-size:12px">#{{ $pedido->numero }}</span></td>
                            <td>{{ $pedido->nome_cliente }}</td>
                            <td>{{ $pedido->vendedor->nome }}</td>
                            <td>{{ $pedido->rota?->codigo ?? '—' }}</td>
                            <td>
                                {{ $pedido->data_saida->format('d/m/Y') }}
                                @if($pedido->is_atrasado)
                                    <span class="badge badge-atrasado" style="margin-left:4px;font-size:10px">Atrasado</span>
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $pedido->prioridade }}">{{ \App\Models\Pedido::labelPrioridade($pedido->prioridade) }}</span></td>
                            <td><span class="badge badge-{{ $pedido->status }}">{{ \App\Models\Pedido::labelStatus($pedido->status) }}</span></td>
                            <td style="color:var(--text-3);font-size:12px">{{ $pedido->created_at->format('d/m H:i') }}</td>
                            <td style="white-space:nowrap">
                                <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-secondary btn-sm">Ver</a>
                                @if($pedido->status === 'rascunho')
                                    <form method="POST" action="{{ route('pedidos.enviar', $pedido) }}" style="display:inline">
                                        @csrf
                                        <button class="btn btn-primary btn-sm">Enviar</button>
                                    </form>
                                @endif
                                @if($pedido->status === 'rascunho' || auth()->user()->temPermissao('pedidos_excluir'))
                                    <form method="POST" action="{{ route('pedidos.destroy', $pedido) }}" style="display:inline"
                                          onsubmit="return confirm('Excluir pedido #{{ $pedido->numero }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Excluir</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" style="text-align:center;color:var(--text-3);padding:24px">Nenhum pedido encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $pedidos->links() }}
@endsection
