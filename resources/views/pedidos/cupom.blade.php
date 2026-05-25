<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cupom #{{ $pedido->numero }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .cupom {
            background: #fff;
            width: 302px; /* 80mm */
            padding: 14px 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
        }

        .linha  { border-top: 1px dashed #999; margin: 8px 0; }
        .dupla  { border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 4px 0; margin: 8px 0; }

        .centro { text-align: center; }
        .dir    { text-align: right; }

        .empresa { font-size: 16px; font-weight: bold; letter-spacing: .04em; }
        .sub-emp { font-size: 10px; color: #555; margin-top: 2px; }

        .numero-pedido { font-size: 32px; font-weight: bold; letter-spacing: .04em; margin: 4px 0; }

        .prio-urgente { color: #991b1b; font-weight: bold; }
        .prio-alta    { color: #92400e; font-weight: bold; }
        .prio-normal  { color: #555; }

        .info-row { display: flex; justify-content: space-between; margin: 3px 0; font-size: 11px; }
        .info-label { color: #666; }
        .info-val   { font-weight: bold; text-align: right; max-width: 60%; }

        .item-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 4px 0; border-bottom: 1px dotted #ccc; font-size: 11px; }
        .item-nome { flex: 1; }
        .item-tipo { font-size: 10px; color: #888; }
        .item-qtd  { text-align: right; font-weight: bold; white-space: nowrap; margin-left: 8px; }

        .check-area { margin-top: 8px; }
        .check-row  { display: flex; align-items: center; gap: 6px; margin: 5px 0; font-size: 11px; }
        .check-box  { width: 14px; height: 14px; border: 1.5px solid #333; border-radius: 2px; flex-shrink: 0; }

        .assina-area { margin-top: 16px; }
        .assina-linha { border-top: 1px solid #333; margin-top: 20px; padding-top: 4px; font-size: 10px; color: #555; text-align: center; }

        .rodape { font-size: 9px; color: #aaa; text-align: center; margin-top: 10px; }

        /* Botões só na tela */
        .btn-bar { display: flex; gap: 8px; margin-bottom: 14px; justify-content: center; }
        .btn { padding: 7px 16px; border-radius: 5px; font-size: 12px; font-weight: bold; cursor: pointer; border: none; font-family: inherit; }
        .btn-print { background: #1a3a2a; color: #fff; }
        .btn-close { background: #eee; color: #333; }

        @media print {
            body    { background: #fff; padding: 0; }
            .cupom  { box-shadow: none; width: 100%; }
            .btn-bar{ display: none !important; }
        }
        @page { size: 80mm auto; margin: 4mm; }
    </style>
</head>
<body>
<div>
    <div class="btn-bar">
        <button class="btn btn-print" onclick="window.print()">&#128438; Imprimir</button>
        <button class="btn btn-close" onclick="window.close()">Fechar</button>
    </div>

    <div class="cupom">
        {{-- Cabeçalho --}}
        <div class="centro">
            <div class="empresa">VORTEK</div>
            <div class="sub-emp">Pedido de Produção</div>
        </div>

        <div class="linha"></div>

        {{-- Número e prioridade --}}
        <div class="centro">
            <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:.08em">Pedido</div>
            <div class="numero-pedido">#{{ $pedido->numero }}</div>
            <div class="prio-{{ $pedido->prioridade }}" style="font-size:13px">
                &#9654; {{ strtoupper(\App\Models\Pedido::labelPrioridade($pedido->prioridade)) }}
            </div>
        </div>

        <div class="linha"></div>

        {{-- Informações --}}
        <div class="info-row">
            <span class="info-label">Cliente</span>
            <span class="info-val">{{ $pedido->nome_cliente }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Saída</span>
            <span class="info-val">{{ $pedido->data_saida->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Rota</span>
            <span class="info-val">{{ $pedido->rota?->codigo ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Responsável</span>
            <span class="info-val">{{ $pedido->confirmadoPorUser?->name ?? auth()->user()?->name ?? '—' }}</span>
        </div>

        @if($pedido->observacoes)
            <div style="margin-top:6px;padding:5px;background:#fffbeb;border:1px dashed #f59e0b;font-size:10px">
                OBS: {{ $pedido->observacoes }}
            </div>
        @endif

        {{-- Itens --}}
        <div class="dupla">
            <div class="centro" style="font-size:10px;font-weight:bold;letter-spacing:.06em;text-transform:uppercase">
                Itens — {{ $pedido->itens->count() }} produto(s)
            </div>
        </div>

        @foreach($pedido->itens as $i => $item)
            <div class="item-row">
                <div class="item-nome">
                    <div>{{ $i + 1 }}. {{ $item->produto->nome }}</div>
                    <div class="item-tipo">{{ $item->tipo === 'fracionado' ? 'Fracionado' : 'Kilo' }}</div>
                </div>
                <div class="item-qtd">
                    {{ number_format($item->quantidade, 3, ',', '.') }}<br>
                    <span style="font-weight:normal;font-size:10px">{{ $item->unidade }}</span>
                </div>
            </div>
        @endforeach

        {{-- Check de conferência --}}
        <div class="linha"></div>
        <div style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px">
            Conferência
        </div>
        @foreach($pedido->itens as $i => $item)
            <div class="check-row">
                <span class="check-box"></span>
                <span>{{ $item->produto->nome }} — {{ number_format($item->quantidade,3,',','.') }} {{ $item->unidade }}</span>
            </div>
        @endforeach

        {{-- Responsáveis --}}
        <div class="assina-area">
            @if($pedido->confirmado_em)
                <div class="assina-linha">
                    Aceito por: <strong>{{ $pedido->confirmadoPorUser?->name ?? 'Painel de Produção' }}</strong><br>
                    <span style="font-size:9px;color:#aaa">{{ $pedido->confirmado_em->format('d/m/Y H:i') }}</span>
                </div>
            @else
                <div class="assina-linha">Aceito por: ________________________</div>
            @endif
            @if($pedido->conferido_em)
                <div class="assina-linha" style="margin-top:16px">
                    Conferido por: <strong>{{ $pedido->conferidoPorUser?->name ?? '—' }}</strong><br>
                    <span style="font-size:9px;color:#aaa">{{ $pedido->conferido_em->format('d/m/Y H:i') }}</span>
                </div>
            @else
                <div class="assina-linha" style="margin-top:20px">Conferido por: _____________________</div>
            @endif
        </div>

        {{-- Rodapé --}}
        <div class="linha"></div>
        <div class="rodape">
            Pedido #{{ $pedido->numero }} &mdash; {{ now()->format('d/m/Y H:i') }}<br>
            VORTEK — Sistema de Controle de Produção
        </div>
    </div>
</div>
</body>
</html>
