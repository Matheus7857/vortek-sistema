<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pedido #{{ $pedido->numero }} — 2 Vias</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111;
            background: #e5e5e5;
        }

        /* ── Tela: centraliza e mostra as duas vias empilhadas ── */
        .screen-wrap {
            max-width: 720px;
            margin: 0 auto;
            padding: 20px;
        }

        /* ── Barra de botões (só tela) ── */
        .btn-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }
        .btn {
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }
        .btn-print { background: #1565e6; color: #fff; }
        .btn-close  { background: #fff;    color: #333; border: 1px solid #ccc; }

        /* ── Folha A4 simulada na tela ── */
        .folha {
            background: #fff;
            width: 100%;
            padding: 8mm;
            box-shadow: 0 3px 16px rgba(0,0,0,.18);
        }

        /* ── Via (meia folha) ── */
        .via {
            padding: 8px 10px 6px;
            border: 1px solid #ccc;
            border-radius: 3px;
            overflow: hidden;
        }

        /* ── Separador de corte ── */
        .corte {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 6px 0;
            color: #aaa;
            font-size: 10px;
        }
        .corte-linha {
            flex: 1;
            border-top: 1.5px dashed #bbb;
        }

        /* ── Cabeçalho da via ── */
        .via-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #1565e6;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .empresa-nome {
            font-size: 15px;
            font-weight: 900;
            color: #1565e6;
            letter-spacing: .04em;
        }
        .empresa-sub {
            font-size: 9px;
            color: #666;
            margin-top: 1px;
        }
        .via-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 3px;
            text-align: center;
            line-height: 1.4;
        }
        .via-1 { background: #1565e6; color: #fff; }
        .via-2 { background: #f1f5f9; color: #1565e6; border: 1px solid #cbd5e1; }

        /* ── Linha de dados do pedido ── */
        .pedido-linha {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f7f4;
            border: 1px solid #e0ddd6;
            border-radius: 4px;
            padding: 5px 8px;
            margin-bottom: 6px;
        }
        .pedido-num {
            font-size: 20px;
            font-weight: 900;
            color: #1565e6;
            font-family: 'Courier New', monospace;
            white-space: nowrap;
        }
        .prio {
            font-size: 9px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .prio-urgente { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .prio-alta    { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .prio-normal  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .pedido-meta  { font-size: 10px; color: #555; margin-left: auto; text-align: right; line-height: 1.6; }

        /* ── Dados do cliente / rota ── */
        .dados-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 12px;
            margin-bottom: 6px;
        }
        .dado {
            display: flex;
            flex-direction: column;
        }
        .dado-label {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #888;
        }
        .dado-val {
            font-size: 11px;
            font-weight: 600;
            color: #111;
        }
        .dado.full { grid-column: 1 / -1; }

        /* ── Tabela de itens ── */
        .titulo-sec {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            margin-bottom: 3px;
        }
        .itens-table {
            width: 100%;
            border-collapse: collapse;
        }
        .itens-table th {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            color: #888;
            padding: 2px 4px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .itens-table th:last-child { text-align: right; }
        .itens-table td {
            font-size: 10px;
            padding: 3px 4px;
            border-bottom: 1px dotted #eee;
            vertical-align: middle;
        }
        .itens-table td:last-child {
            text-align: right;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            white-space: nowrap;
        }
        .item-tipo {
            font-size: 8px;
            color: #888;
        }

        /* ── Observações ── */
        .obs-box {
            margin-top: 5px;
            background: #fffbeb;
            border: 1px dashed #f59e0b;
            border-radius: 3px;
            padding: 4px 7px;
            font-size: 10px;
            color: #92400e;
        }

        /* ── Rodapé da via ── */
        .via-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 8px;
            gap: 20px;
        }
        .assina {
            flex: 1;
            border-top: 1px solid #333;
            padding-top: 4px;
            font-size: 9px;
            color: #555;
        }
        .carimbo {
            width: 90px;
            height: 40px;
            border: 1px dashed #bbb;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #bbb;
            text-align: center;
            line-height: 1.3;
        }

        /* ── Rodapé do documento ── */
        .doc-footer {
            font-size: 8px;
            color: #aaa;
            text-align: right;
            margin-top: 3px;
        }

        /* ──────────────── IMPRESSÃO ──────────────── */
        @media print {
            body        { background: #fff; }
            .screen-wrap{ padding: 0; max-width: none; }
            .folha      { box-shadow: none; padding: 4mm; }
            .btn-bar    { display: none !important; }
            .via        { border-color: #ddd; }
        }

        @page {
            size: A4 portrait;
            margin: 6mm;
        }
    </style>
</head>
<body>
<div class="screen-wrap">

    {{-- Botões (só tela) --}}
    <div class="btn-bar">
        <button class="btn btn-print" onclick="window.print()">&#128438; Imprimir 2 Vias</button>
        <button class="btn btn-close" onclick="window.close()">Fechar</button>
    </div>

    <div class="folha">

        @for($via = 1; $via <= 2; $via++)

        {{-- ── VIA ── --}}
        <div class="via">

            {{-- Cabeçalho --}}
            <div class="via-header">
                <div>
                    <div class="empresa-nome">VORTEK</div>
                    <div class="empresa-sub">Pedido de Faturamento</div>
                </div>
                <div class="via-badge {{ $via === 1 ? 'via-1' : 'via-2' }}">
                    {{ $via }}ª VIA<br>
                    <span style="font-size:8px;font-weight:400">{{ $via === 1 ? 'PRODUÇÃO' : 'ESCRITÓRIO' }}</span>
                </div>
            </div>

            {{-- Número + prioridade --}}
            <div class="pedido-linha">
                <div class="pedido-num">#{{ $pedido->numero }}</div>
                <span class="prio prio-{{ $pedido->prioridade }}">
                    {{ \App\Models\Pedido::labelPrioridade($pedido->prioridade) }}
                </span>
                <div class="pedido-meta">
                    Emitido: {{ now()->format('d/m/Y H:i') }}<br>
                    por: {{ auth()->user()->name }}
                </div>
            </div>

            {{-- Dados --}}
            <div class="dados-grid">
                <div class="dado full">
                    <span class="dado-label">Cliente</span>
                    <span class="dado-val" style="font-size:13px">{{ $pedido->nome_cliente }}</span>
                </div>
                <div class="dado">
                    <span class="dado-label">Data de Saída</span>
                    <span class="dado-val">{{ $pedido->data_saida->format('d/m/Y') }}</span>
                </div>
                <div class="dado">
                    <span class="dado-label">Rota</span>
                    <span class="dado-val">
                        @if($pedido->rota)
                            {{ $pedido->rota->codigo }} — {{ $pedido->rota->nome }}
                        @else
                            —
                        @endif
                    </span>
                </div>
            </div>

            {{-- Itens --}}
            <div class="titulo-sec">Produtos — {{ $pedido->itens->count() }} item(ns)</div>
            <table class="itens-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Qtd</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedido->itens as $i => $item)
                    <tr>
                        <td style="color:#aaa">{{ $i + 1 }}</td>
                        <td><strong>{{ $item->produto->nome }}</strong></td>
                        <td><span class="item-tipo">{{ $item->tipo === 'fracionado' ? 'Fracionado' : 'Kilo' }}</span></td>
                        <td>{{ number_format($item->quantidade, 3, ',', '.') }} {{ $item->unidade }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Observações --}}
            @if($pedido->observacoes)
                <div class="obs-box">
                    <strong>OBS:</strong> {{ $pedido->observacoes }}
                </div>
            @endif

            {{-- Rodapé com assinatura --}}
            <div class="via-footer">
                <div class="assina">
                    Recebido por / Assinatura<br>
                    <span style="font-size:8px;color:#aaa">Nome legível: _________________________</span>
                </div>
                <div class="assina">
                    Data: ____/____/________ &nbsp; Hora: ____:____
                </div>
                <div class="carimbo">Carimbo<br>empresa</div>
            </div>

            <div class="doc-footer">Pedido #{{ $pedido->numero }} &mdash; {{ now()->format('d/m/Y H:i') }} &mdash; VORTEK</div>

        </div>
        {{-- /via --}}

        @if($via === 1)
        {{-- Linha de corte --}}
        <div class="corte">
            <div class="corte-linha"></div>
            <span>&#9988; cortar aqui</span>
            <div class="corte-linha"></div>
        </div>
        @endif

        @endfor

    </div>{{-- /folha --}}
</div>
</body>
</html>
