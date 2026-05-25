<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Painel de Produção — VORTEK</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:    #0b1829;
            --blue:    #1565e6;
            --blue-m:  #2979ff;
            --blue-b:  #4da8f7;
            --danger:  #dc2626;
            --ok:      #16a34a;
            --warn:    #d97706;
            --bg:      #f1f5f9;
            --card:    #ffffff;
            --border:  #e2e8f0;
            --txt:     #0f172a;
            --txt2:    #475569;
            --txt3:    #94a3b8;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'IBM Plex Sans', sans-serif; background: var(--bg); color: var(--txt); }

        /* ── Header ── */
        .header {
            background: linear-gradient(90deg, #0b1829 0%, #0d1f38 100%);
            color: #fff;
            padding: 0 24px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            position: relative;
            box-shadow: 0 2px 12px rgba(0,0,0,.25);
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #1565e6, #4da8f7, #1565e6, transparent);
        }
        .header-brand {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: .06em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-v {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #fff 30%, #4da8f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-sep { color: rgba(255,255,255,.2); }
        .brand-label { font-size: 13px; color: rgba(255,255,255,.6); font-weight: 500; letter-spacing: .04em; }
        .header-right  { display: flex; align-items: center; gap: 20px; }
        .header-clock  {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 15px;
            color: rgba(255,255,255,.75);
            background: rgba(255,255,255,.06);
            padding: 5px 14px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,.1);
        }
        .header-refresh {
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.14);
            color: rgba(255,255,255,.65);
            padding: 5px 13px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-family: inherit;
            transition: background .15s;
        }
        .header-refresh:hover { background: rgba(255,255,255,.14); color: #fff; }

        /* ── Board ── */
        .board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            height: calc(100vh - 58px);
            overflow: hidden;
        }
        .col {
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border);
            overflow: hidden;
            background: var(--bg);
        }
        .col:last-child { border-right: none; }

        /* ── Cabeçalho das colunas ── */
        .col-header {
            padding: 14px 16px 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
            background: var(--card);
            border-bottom: 3px solid var(--border);
        }
        .col-pendente  .col-header { border-bottom-color: var(--danger); }
        .col-producao  .col-header { border-bottom-color: var(--warn); }
        .col-pronto    .col-header { border-bottom-color: var(--ok); }

        .col-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
        }
        .col-pendente .col-title { color: var(--danger); }
        .col-producao .col-title { color: var(--warn); }
        .col-pronto   .col-title { color: var(--ok); }

        .col-count {
            background: #f1f5f9;
            color: var(--txt3);
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 12px;
            font-weight: 700;
            font-family: 'IBM Plex Mono', monospace;
            min-width: 28px;
            text-align: center;
        }
        .col-count.has-items {
            background: var(--danger);
            color: #fff;
            box-shadow: 0 0 8px rgba(220,38,38,.35);
        }

        .dot-pulse {
            display: inline-block;
            width: 8px; height: 8px;
            background: var(--danger);
            border-radius: 50%;
            box-shadow: 0 0 6px var(--danger);
            animation: pulse 1.2s infinite;
            margin-left: 2px;
        }
        @keyframes pulse {
            0%,100% { opacity:1; transform:scale(1); }
            50%      { opacity:.35; transform:scale(.65); }
        }

        /* ── Corpo da coluna ── */
        .col-body {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .col-body::-webkit-scrollbar { width: 4px; }
        .col-body::-webkit-scrollbar-track { background: transparent; }
        .col-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        /* ── Cards ── */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px;
            border-left: 4px solid var(--border);
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            transition: box-shadow .15s;
        }
        .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1); }
        .card.urgente { border-left-color: var(--danger); }
        .card.alta    { border-left-color: #f59e0b; }
        .card.normal  { border-left-color: #cbd5e1; }

        .card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .card-num  {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            color: var(--txt3);
            letter-spacing: .04em;
        }
        .card-nome { font-size: 16px; font-weight: 700; margin-top: 3px; line-height: 1.2; color: var(--txt); }
        .card-meta { font-size: 12px; color: var(--txt2); margin-top: 4px; }

        .badge {
            display: inline-block; padding: 3px 9px; border-radius: 10px;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
            white-space: nowrap;
        }
        .badge-urgente { background: #fee2e2; color: var(--danger); }
        .badge-alta    { background: #fef3c7; color: #92400e; }
        .badge-normal  { background: #f1f5f9; color: var(--txt3); }

        /* ── Itens ── */
        .itens { list-style: none; margin-bottom: 12px; }
        .itens li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }
        .itens li:last-child { border-bottom: none; }
        .item-nome { font-weight: 500; color: var(--txt); }
        .item-qtd  {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            color: var(--blue);
            white-space: nowrap;
            font-weight: 600;
        }

        /* ── Observações ── */
        .card-obs {
            background: #fffbeb;
            border: 1px dashed #f59e0b;
            border-radius: 6px;
            padding: 7px 10px;
            font-size: 12px;
            margin-bottom: 10px;
            color: #92400e;
            line-height: 1.4;
        }

        /* ── Botões de ação ── */
        .btn-aceitar {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--danger), #ef4444);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: opacity .15s, transform .1s;
            box-shadow: 0 3px 10px rgba(220,38,38,.3);
        }
        .btn-aceitar:active { opacity: .85; transform: scale(.98); }

        .btn-pronto {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--ok), #22c55e);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: opacity .15s, transform .1s;
            box-shadow: 0 3px 10px rgba(22,163,74,.3);
        }
        .btn-pronto:active { opacity: .85; transform: scale(.98); }

        .btn-cupom {
            width: 100%;
            padding: 9px;
            background: rgba(21,101,230,.06);
            color: var(--blue);
            border: 1px solid rgba(21,101,230,.2);
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            margin-bottom: 7px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: background .15s;
        }
        .btn-cupom:hover { background: rgba(21,101,230,.12); }

        .btn-voltar {
            width: 100%;
            padding: 8px;
            background: transparent;
            color: var(--txt3);
            border: 1px dashed var(--border);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            margin-top: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all .15s;
        }
        .btn-voltar:hover { background: #f8fafc; color: var(--txt2); border-color: var(--border); }

        /* ── Vazio ── */
        .empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--txt3);
            padding: 32px 16px;
            text-align: center;
            gap: 8px;
        }
        .empty-icon { font-size: 38px; opacity: .5; }
        .empty-txt  { font-size: 13px; }

        /* ── Badge pronto ── */
        .pronto-check {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            box-shadow: 0 2px 8px rgba(22,163,74,.3);
            flex-shrink: 0;
        }

        /* ── Modal confirmação ── */
        .modal-bg {
            display: none;
            position: fixed; inset: 0;
            background: rgba(15,23,42,.55);
            z-index: 100;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px);
        }
        .modal-bg.open { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 16px;
            padding: 32px 28px;
            width: 90%;
            max-width: 380px;
            text-align: center;
            box-shadow: 0 24px 60px rgba(0,0,0,.2);
            border: 1px solid var(--border);
        }
        .modal-icon  { font-size: 40px; margin-bottom: 12px; }
        .modal-title { font-size: 20px; font-weight: 700; margin-bottom: 6px; color: var(--txt); }
        .modal-sub   { font-size: 14px; color: var(--txt2); margin-bottom: 24px; line-height: 1.5; }
        .modal-btns  { display: flex; gap: 10px; }
        .modal-btns button {
            flex: 1; padding: 13px; border-radius: 8px;
            font-size: 15px; font-weight: 700; border: none;
            cursor: pointer; font-family: inherit; transition: opacity .15s;
        }
        .modal-btns button:hover { opacity: .88; }
        .btn-sim { background: linear-gradient(135deg, var(--ok), #22c55e); color: #fff; box-shadow: 0 3px 10px rgba(22,163,74,.3); }
        .btn-nao { background: #f1f5f9; color: var(--txt2); }

        /* ── Abas mobile ── */
        .mobile-tabs {
            display: none;
            background: var(--card);
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .tab-btn {
            flex: 1;
            padding: 13px 8px 11px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--txt3);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            transition: color .15s, border-color .15s;
        }
        .tab-btn.active.tab-pendente { color: var(--danger); border-bottom-color: var(--danger); }
        .tab-btn.active.tab-producao { color: var(--warn);   border-bottom-color: var(--warn); }
        .tab-btn.active.tab-pronto   { color: var(--ok);     border-bottom-color: var(--ok); }
        .tab-badge {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 20px; height: 20px;
            border-radius: 10px; padding: 0 6px;
            font-size: 12px; font-weight: 700;
            background: #f1f5f9; color: var(--txt3);
        }
        .tab-btn.active.tab-pendente .tab-badge { background: var(--danger); color: #fff; }
        .tab-btn.active.tab-producao .tab-badge { background: var(--warn);   color: #fff; }
        .tab-btn.active.tab-pronto   .tab-badge { background: var(--ok);     color: #fff; }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .mobile-tabs { display: flex; }
            .header { height: 52px; padding: 0 14px; }
            .header-clock { font-size: 12px; padding: 4px 10px; }
            .header-refresh { padding: 4px 10px; font-size: 12px; }

            .board {
                display: block;
                height: calc(100vh - 52px - 52px);
                overflow: hidden;
            }
            .col {
                display: none;
                height: 100%;
                border-right: none;
                overflow: hidden;
            }
            .col.tab-active { display: flex; }
            .col-header { display: none; } /* título já está nas abas */
            .col-body { max-height: 100%; padding: 10px; gap: 10px; }

            /* Touch-friendly */
            .btn-aceitar, .btn-pronto { font-size: 16px; padding: 16px; min-height: 54px; }
            .btn-cupom   { padding: 12px; font-size: 14px; min-height: 46px; }
            .btn-voltar  { padding: 12px; font-size: 13px; min-height: 44px; }
            .card-nome   { font-size: 18px; }
            .item-nome, .item-qtd { font-size: 14px; }
            .itens li    { padding: 8px 0; }

            .brand-label { display: none; }
        }

        @media (max-width: 400px) {
            .tab-btn { font-size: 11px; padding: 11px 4px 9px; }
        }
    </style>
</head>
<body>

<header class="header">
    <div class="header-brand">
        <span class="brand-v">V</span>
        <span class="brand-sep">|</span>
        <span class="brand-label">VORTEK &mdash; Painel de Produção</span>
    </div>
    <div class="header-right">
        <button class="header-refresh" onclick="location.reload()">&#8635; Atualizar</button>
        <div class="header-clock" id="clock"></div>
    </div>
</header>

{{-- Abas (visíveis só no mobile) --}}
<nav class="mobile-tabs" id="mobile-tabs">
    <button class="tab-btn tab-pendente active" onclick="switchTab('pendente')">
        Pendentes
        <span class="tab-badge">{{ $pendentes->count() }}</span>
    </button>
    <button class="tab-btn tab-producao" onclick="switchTab('producao')">
        Produção
        <span class="tab-badge">{{ $producao->count() }}</span>
    </button>
    <button class="tab-btn tab-pronto" onclick="switchTab('pronto')">
        Prontos
        <span class="tab-badge">{{ $prontos->count() }}</span>
    </button>
</nav>

<div class="board">

    {{-- PENDENTES --}}
    <div class="col col-pendente tab-active" id="col-pendente">
        <div class="col-header">
            <span class="col-title">Pendentes</span>
            <span class="col-count {{ $pendentes->count() ? 'has-items' : '' }}">{{ $pendentes->count() }}</span>
            @if($pendentes->count()) <span class="dot-pulse"></span> @endif
        </div>
        <div class="col-body">
            @forelse($pendentes as $pedido)
                <div class="card {{ $pedido->prioridade }}">
                    <div class="card-top">
                        <div>
                            <div class="card-num">#{{ $pedido->numero }}</div>
                            <div class="card-nome">{{ $pedido->nome_cliente }}</div>
                            <div class="card-meta">
                                Saída {{ $pedido->data_saida->format('d/m') }}
                                @if($pedido->rota) &middot; {{ $pedido->rota->codigo }} @endif
                            </div>
                        </div>
                        <span class="badge badge-{{ $pedido->prioridade }}">{{ \App\Models\Pedido::labelPrioridade($pedido->prioridade) }}</span>
                    </div>

                    <ul class="itens">
                        @foreach($pedido->itens as $item)
                            <li>
                                <span class="item-nome">{{ $item->produto->nome }}</span>
                                <span class="item-qtd">{{ number_format($item->quantidade,3,',','.') }} {{ $item->unidade }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @if($pedido->observacoes)
                        <div class="card-obs">&#9888; {{ $pedido->observacoes }}</div>
                    @endif

                    <form id="form-aceitar-{{ $pedido->id }}"
                          method="POST"
                          action="{{ route('painel.aceitar', $pedido) }}">
                        @csrf
                        <button type="button" class="btn-aceitar"
                                onclick="aceitarPedido({{ $pedido->id }}, '{{ route('pedidos.cupom', $pedido) }}')">
                            &#128438; Aceitar &amp; Imprimir
                        </button>
                    </form>
                </div>
            @empty
                <div class="empty">
                    <div class="empty-icon">&#10003;</div>
                    <div class="empty-txt">Sem pedidos pendentes</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- EM PRODUÇÃO --}}
    <div class="col col-producao" id="col-producao">
        <div class="col-header">
            <span class="col-title">Em Produção</span>
            <span class="col-count">{{ $producao->count() }}</span>
        </div>
        <div class="col-body">
            @forelse($producao as $pedido)
                <div class="card {{ $pedido->prioridade }}">
                    <div class="card-top">
                        <div>
                            <div class="card-num">#{{ $pedido->numero }}</div>
                            <div class="card-nome">{{ $pedido->nome_cliente }}</div>
                            <div class="card-meta">
                                Saída {{ $pedido->data_saida->format('d/m') }}
                                @if($pedido->rota) &middot; {{ $pedido->rota->codigo }} @endif
                            </div>
                        </div>
                        <span class="badge badge-{{ $pedido->prioridade }}">{{ \App\Models\Pedido::labelPrioridade($pedido->prioridade) }}</span>
                    </div>

                    <ul class="itens">
                        @foreach($pedido->itens as $item)
                            <li>
                                <span class="item-nome">{{ $item->produto->nome }}</span>
                                <span class="item-qtd">{{ number_format($item->quantidade,3,',','.') }} {{ $item->unidade }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @if($pedido->observacoes)
                        <div class="card-obs">&#9888; {{ $pedido->observacoes }}</div>
                    @endif

                    <a href="{{ route('pedidos.cupom', $pedido) }}" target="_blank" class="btn-cupom">
                        &#128438; Reimprimir Cupom
                    </a>

                    <button class="btn-pronto"
                            onclick="confirmarPronto({{ $pedido->id }}, '{{ addslashes($pedido->nome_cliente) }}', '#{{ $pedido->numero }}')">
                        &#10003; Marcar como Pronto
                    </button>

                    <form id="form-pronto-{{ $pedido->id }}"
                          method="POST"
                          action="{{ route('painel.pronto', $pedido) }}"
                          style="display:none">
                        @csrf
                    </form>
                </div>
            @empty
                <div class="empty">
                    <div class="empty-icon">&#9203;</div>
                    <div class="empty-txt">Nenhum pedido em produção</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- PRONTOS --}}
    <div class="col col-pronto" id="col-pronto">
        <div class="col-header">
            <span class="col-title">Prontos</span>
            <span class="col-count">{{ $prontos->count() }}</span>
        </div>
        <div class="col-body">
            @forelse($prontos as $pedido)
                <div class="card normal" style="opacity:.88">
                    <div class="card-top">
                        <div>
                            <div class="card-num">#{{ $pedido->numero }}</div>
                            <div class="card-nome">{{ $pedido->nome_cliente }}</div>
                            <div class="card-meta">
                                Saída {{ $pedido->data_saida->format('d/m') }}
                                @if($pedido->rota) &middot; {{ $pedido->rota->codigo }} @endif
                            </div>
                        </div>
                        <div class="pronto-check">&#10003;</div>
                    </div>
                    <div style="font-size:12px;color:var(--txt3)">{{ $pedido->itens->count() }} iten(s) &middot; Concluído</div>
                </div>
            @empty
                <div class="empty">
                    <div class="empty-icon">&#128230;</div>
                    <div class="empty-txt">Nenhum pronto ainda</div>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal confirmação Pronto --}}
<div class="modal-bg" id="modal-pronto">
    <div class="modal-box">
        <div class="modal-icon">&#9989;</div>
        <div class="modal-title">Confirmar Conclusão</div>
        <div class="modal-sub" id="modal-pronto-txt">Marcar pedido como pronto?</div>
        <div class="modal-btns">
            <button class="btn-nao" onclick="document.getElementById('modal-pronto').classList.remove('open')">
                Cancelar
            </button>
            <button class="btn-sim" id="btn-sim-pronto">
                &#10003; Confirmar
            </button>
        </div>
    </div>
</div>

<script>
    // Navegação por abas (mobile)
    function switchTab(name) {
        // Colunas
        ['pendente','producao','pronto'].forEach(t => {
            document.getElementById('col-' + t).classList.toggle('tab-active', t === name);
        });
        // Botões
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.classList.contains('tab-' + name));
        });
    }

    // Relógio
    function tick() {
        const n = new Date();
        document.getElementById('clock').textContent =
            n.toLocaleDateString('pt-BR', {weekday:'short', day:'2-digit', month:'short'}) + '  ' +
            n.toLocaleTimeString('pt-BR', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
    }
    tick(); setInterval(tick, 1000);

    // Aceitar: abre cupom + submete form
    function aceitarPedido(id, cupomUrl) {
        window.open(cupomUrl, '_blank');
        setTimeout(() => document.getElementById('form-aceitar-' + id).submit(), 400);
    }

    // Pronto: mostra modal de confirmação
    function confirmarPronto(id, nome, num) {
        document.getElementById('modal-pronto-txt').textContent =
            'Pedido ' + num + ' — ' + nome;
        document.getElementById('btn-sim-pronto').onclick = function() {
            document.getElementById('form-pronto-' + id).submit();
        };
        document.getElementById('modal-pronto').classList.add('open');
    }

    // ── Notificação sonora de novos pedidos ──────────────────────────────
    const PEDIDOS_KEY   = 'painel_pendentes_count';
    const currentCount  = {{ $pendentes->count() }};
    const previousCount = parseInt(localStorage.getItem(PEDIDOS_KEY) ?? '-1');
    let audioCtx        = null;

    function getAudio() {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') audioCtx.resume();
        return audioCtx;
    }
    document.addEventListener('click',      () => getAudio(), { once: true });
    document.addEventListener('touchstart', () => getAudio(), { once: true });

    function playBeep(freq, startTime, duration) {
        const ctx  = getAudio();
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type            = 'sine';
        osc.frequency.value = freq;
        gain.gain.setValueAtTime(0, ctx.currentTime + startTime);
        gain.gain.linearRampToValueAtTime(0.35, ctx.currentTime + startTime + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + startTime + duration);
        osc.start(ctx.currentTime + startTime);
        osc.stop(ctx.currentTime + startTime + duration + 0.05);
    }

    function tocarAlerta() {
        playBeep(523, 0.0,  0.18);
        playBeep(659, 0.22, 0.18);
        playBeep(784, 0.44, 0.30);
    }

    localStorage.setItem(PEDIDOS_KEY, currentCount);
    if (previousCount >= 0 && currentCount > previousCount) tocarAlerta();

    // Auto-atualiza a cada 20 segundos
    setTimeout(() => location.reload(), 20000);
</script>
</body>
</html>
