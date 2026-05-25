@extends('layouts.app')

@section('page-title', 'Relatório')

@section('topbar-actions')
    <button onclick="window.print()" class="btn btn-secondary btn-sm no-print">&#128438; Imprimir</button>
@endsection

@section('content')

{{-- Filtros --}}
<div class="card no-print" style="margin-bottom:16px">
    <div class="card-header" style="padding:12px 16px">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;width:100%">
            <div class="form-group" style="margin:0">
                <label class="form-label" style="margin-bottom:3px">Data de Saída</label>
                <input type="date" name="data" class="form-control" value="{{ $data }}" style="width:160px">
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label" style="margin-bottom:3px">Rota</label>
                <select name="rota" class="form-control" style="width:140px">
                    <option value="">Todas</option>
                    @foreach($rotas as $rota)
                        <option value="{{ $rota->id }}" @selected($rotaId == $rota->id)>{{ $rota->codigo }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
        </form>
    </div>
</div>

@php $dataFormatada = \Carbon\Carbon::parse($data)->format('d/m/Y'); @endphp

@if($pedidos->isEmpty())
    <div class="alert alert-warn">Nenhum pedido para {{ $dataFormatada }}.</div>
@else

<div class="card">
    <div class="card-header">
        <span class="card-title">Pedidos — {{ $dataFormatada }}</span>
        <span style="margin-left:auto;font-size:12px;color:var(--text-3)">{{ $pedidos->count() }} pedido(s)</span>
    </div>

    @foreach($pedidos as $pedido)
        <div style="padding:14px 16px;border-bottom:1px solid var(--border)">

            {{-- Nome do cliente --}}
            <div style="font-size:16px;font-weight:700;margin-bottom:8px">
                {{ $pedido->nome_cliente }}
                @if($pedido->rota)
                    <span style="font-size:12px;font-weight:400;color:var(--text-3);margin-left:6px">{{ $pedido->rota->codigo }}</span>
                @endif
            </div>

            {{-- Produtos --}}
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:4px">
                @foreach($pedido->itens as $item)
                    <li style="display:flex;justify-content:space-between;font-size:14px;padding:4px 0;border-bottom:1px dotted var(--border)">
                        <span>{{ $item->produto->nome }}</span>
                        <span style="font-family:'IBM Plex Mono',monospace;font-weight:600">
                            {{ number_format($item->quantidade, 3, ',', '.') }} {{ $item->unidade }}
                        </span>
                    </li>
                @endforeach
            </ul>

        </div>
    @endforeach
</div>

@endif

@endsection

@push('styles')
<style>
@media print {
    .no-print, .sidebar, .topbar { display: none !important; }
    .page-body { padding: 0; overflow: visible; }
    .main-wrap { overflow: visible; }
    .card { box-shadow: none; border: 1px solid #ccc; break-inside: avoid; }
}
</style>
@endpush
