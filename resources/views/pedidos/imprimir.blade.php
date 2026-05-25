<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #{{ $pedido->numero }} — Produção</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'IBM Plex Sans', sans-serif; font-size: 13px; color: #111; background: #fff; }

        .page { max-width: 800px; margin: 0 auto; padding: 28px 32px; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #1a3a2a; }
        .header-left h1 { font-family: 'IBM Plex Mono', monospace; font-size: 20px; font-weight: 600; color: #1a3a2a; }
        .header-left p  { font-size: 11px; color: #666; margin-top: 3px; }
        .header-right   { text-align: right; }
        .numero-pedido  { font-family: 'IBM Plex Mono', monospace; font-size: 28px; font-weight: 600; color: #1a3a2a; }
        .numero-label   { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: .06em; }

        .prio { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; margin-top: 4px; }
        .prio-urgente { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .prio-alta    { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .prio-normal  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
        .info-box { background: #f8f7f4; border: 1px solid #e0ddd6; border-radius: 6px; padding: 10px 12px; }
        .info-label { font-size: 10px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 3px; }
        .info-value { font-size: 14px; font-weight: 500; }
        .info-box.destaque { border-color: #1a3a2a; background: #e8f0ec; }
        .info-box.destaque .info-value { color: #1a3a2a; font-weight: 700; }

        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #555; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { padding: 8px 10px; text-align: left; font-size: 11px; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: .05em; background: #f8f7f4; border: 1px solid #e0ddd6; }
        td { padding: 10px 10px; border: 1px solid #e0ddd6; vertical-align: middle; }
        tr:nth-child(even) td { background: #fafaf8; }
        .check-cell { width: 40px; text-align: center; }
        .check-box  { width: 20px; height: 20px; border: 2px solid #1a3a2a; border-radius: 3px; display: inline-block; }
        .item-nome  { font-weight: 600; font-size: 14px; }
        .item-tipo  { font-size: 11px; color: #888; }
        .item-qtd   { font-family: 'IBM Plex Mono', monospace; font-size: 14px; font-weight: 600; text-align: right; }

        .obs-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 10px 14px; margin-bottom: 20px; font-size: 13px; }
        .obs-box .obs-label { font-size: 10px; font-weight: 700; color: #92400e; text-transform: uppercase; margin-bottom: 4px; }

        .assinaturas { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; margin-top: 8px; }
        .assinatura  { border-top: 1px solid #333; padding-top: 8px; }
        .assinatura-label { font-size: 11px; font-weight: 600; color: #555; text-transform: uppercase; letter-spacing: .05em; }
        .assinatura-sub   { font-size: 10px; color: #999; margin-top: 3px; }
        .assinatura-data  { font-size: 11px; color: #888; margin-top: 4px; }

        .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 10px; color: #aaa; display: flex; justify-content: space-between; }

        .btn-bar { display: flex; gap: 10px; margin-bottom: 24px; }
        .btn { padding: 8px 18px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; text-decoration: none; border: 1px solid transparent; display: inline-flex; align-items: center; gap: 6px; }
        .btn-print { background: #1a3a2a; color: #fff; }
        .btn-back  { background: #fff; color: #333; border-color: #ccc; }

        @media print {
            .btn-bar { display: none !important; }
            .page    { padding: 12px 16px; }
            body     { font-size: 12px; }
        }
        @page { size: A4; margin: 12mm; }
    </style>
</head>
<body>
<div class="page">

    <div class="btn-bar">
        <button onclick="window.print()" class="btn btn-print">&#128438; Imprimir</button>
        <a href="javascript:history.back()" class="btn btn-back">&#8592; Voltar</a>
    </div>

    <div class="header">
        <div class="header-left">
            <h1>VORTEK — Pedido de Produção</h1>
            <p>Emitido em {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name }}</p>
        </div>
        <div class="header-right">
            <div class="numero-label">Pedido</div>
            <div class="numero-pedido">#{{ $pedido->numero }}</div>
            <span class="prio prio-{{ $pedido->prioridade }}">
                {{ \App\Models\Pedido::labelPrioridade($pedido->prioridade) }}
            </span>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box destaque">
            <div class="info-label">Cliente</div>
            <div class="info-value">{{ $pedido->nome_cliente }}</div>
        </div>
        <div class="info-box destaque">
            <div class="info-label">Data de Saída</div>
            <div class="info-value">
                {{ $pedido->data_saida->format('d/m/Y') }}
                @if($pedido->rota) &mdash; {{ $pedido->rota->codigo }} @endif
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Status</div>
            <div class="info-value">{{ \App\Models\Pedido::labelStatus($pedido->status) }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Faturista</div>
            <div class="info-value">{{ $pedido->vendedor->nome }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Rota / Motorista</div>
            <div class="info-value">
                @if($pedido->rota)
                    {{ $pedido->rota->nome }}<br>
                    <span style="font-size:12px;color:#666">{{ $pedido->rota->motorista ?? '—' }}</span>
                @else —
                @endif
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Emitido em</div>
            <div class="info-value" style="font-size:13px">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    @if($pedido->observacoes)
        <div class="obs-box">
            <div class="obs-label">Observações do Pedido</div>
            {{ $pedido->observacoes }}
        </div>
    @endif

    <div class="section-title">Itens a Produzir — {{ $pedido->itens->count() }} item(ns)</div>
    <table>
        <thead>
            <tr>
                <th class="check-cell">OK</th>
                <th>#</th>
                <th>Produto</th>
                <th>Tipo</th>
                <th style="text-align:right">Quantidade</th>
                <th style="min-width:120px">Observações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->itens as $i => $item)
                <tr>
                    <td class="check-cell"><span class="check-box"></span></td>
                    <td style="color:#aaa;font-size:12px">{{ $i + 1 }}</td>
                    <td>
                        <div class="item-nome">{{ $item->produto->nome }}</div>
                        <div class="item-tipo">{{ ucfirst($item->produto->categoria) }}</div>
                    </td>
                    <td>{{ $item->tipo === 'fracionado' ? 'Fracionado' : 'Kilo' }}</td>
                    <td class="item-qtd">{{ number_format($item->quantidade, 3, ',', '.') }} {{ $item->unidade }}</td>
                    <td style="color:#ccc">&nbsp;</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="assinaturas">
        <div class="assinatura">
            <div class="assinatura-label">Recebido pela Produção</div>
            <div class="assinatura-sub">Nome / Assinatura</div>
            <div class="assinatura-data">Data: ____/____/________ &nbsp; Hora: ____:____</div>
        </div>
        <div class="assinatura">
            <div class="assinatura-label">Conferência / Qualidade</div>
            <div class="assinatura-sub">Nome / Assinatura</div>
            <div class="assinatura-data">Data: ____/____/________ &nbsp; Hora: ____:____</div>
        </div>
    </div>

    <div class="footer">
        <span>VORTEK — Sistema de Controle de Produção</span>
        <span>Pedido #{{ $pedido->numero }} &mdash; {{ now()->format('d/m/Y H:i') }}</span>
    </div>

</div>
</body>
</html>
