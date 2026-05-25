<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VORTEK') — Sistema de Controle de Produção</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent:       #1565e6;
            --accent-light: #e8f0ff;
            --accent-mid:   #2979ff;
            --danger:       #dc2626;
            --danger-light: #fef2f2;
            --ok:           #16a34a;
            --ok-light:     #f0fdf4;
            --warn:         #d97706;
            --warn-light:   #fffbeb;
            --blue:         #1565e6;
            --blue-light:   #eff6ff;
            --bg:           #f1f5f9;
            --surface:      #ffffff;
            --text:         #0f172a;
            --text-2:       #475569;
            --text-3:       #94a3b8;
            --border:       #e2e8f0;
            --border-dark:  #cbd5e1;
            --sidebar-w:    224px;
            --sidebar-w-sm: 54px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'IBM Plex Sans', sans-serif; background: var(--bg); color: var(--text); font-size: 14px; }

        /* ── Layout ── */
        .app-shell { display: flex; height: 100vh; overflow: hidden; }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w-sm);
            flex-shrink: 0;
            background: linear-gradient(180deg, #0b1829 0%, #0d1f38 100%);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width .22s cubic-bezier(.4,0,.2,1);
            position: relative;
            z-index: 10;
            box-shadow: 2px 0 12px rgba(0,0,0,.15);
        }
        .sidebar:hover { width: var(--sidebar-w); }

        .sidebar-header {
            padding: 16px 13px 12px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            overflow: hidden;
            flex-shrink: 0;
        }
        .sidebar-logo {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            line-height: 1;
            background: linear-gradient(135deg, #fff 40%, #4da8f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .sidebar-brand-text { opacity: 0; width: 0; overflow: hidden; transition: opacity .18s, width .22s; }
        .sidebar:hover .sidebar-brand-text { opacity: 1; width: auto; }
        .sidebar-brand { font-family: 'IBM Plex Mono', monospace; font-size: 12px; font-weight: 700; color: #fff; letter-spacing: .08em; text-transform: uppercase; white-space: nowrap; }
        .sidebar-sub   { font-size: 9px; color: rgba(255,255,255,.38); margin-top: 2px; white-space: nowrap; letter-spacing: .04em; }

        .sidebar-nav { flex: 1; overflow-y: auto; overflow-x: hidden; padding: 8px 0; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 3px; }

        .sidebar-section {
            padding: 0; height: 0;
            font-size: 9px; font-weight: 700;
            color: rgba(255,255,255,.28);
            letter-spacing: .12em; text-transform: uppercase;
            white-space: nowrap; overflow: hidden;
            opacity: 0; pointer-events: none;
            transition: opacity .18s;
        }
        .sidebar:hover .sidebar-section { opacity: 1; height: auto; padding: 10px 0 4px 14px; pointer-events: auto; }

        .nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 14px;
            color: rgba(255,255,255,.55);
            text-decoration: none; font-size: 13px;
            transition: background .12s, color .12s;
            border-left: 2px solid transparent;
            white-space: nowrap; overflow: hidden; position: relative;
            border-radius: 0;
        }
        .nav-link:hover { background: rgba(255,255,255,.07); color: rgba(255,255,255,.9); }
        .nav-link.active {
            background: rgba(21,101,230,.25);
            color: #fff;
            border-left-color: #4da8f7;
        }

        .nav-icon  { flex-shrink: 0; width: 18px; text-align: center; font-size: 14px; }
        .nav-label { opacity: 0; width: 0; overflow: hidden; transition: opacity .18s; white-space: nowrap; }
        .sidebar:hover .nav-label { opacity: 1; width: auto; }

        .nav-badge {
            margin-left: auto; background: #dc2626; color: #fff;
            border-radius: 9px; padding: 1px 6px;
            font-size: 10px; font-weight: 600; flex-shrink: 0;
            opacity: 0; transition: opacity .18s;
        }
        .sidebar:hover .nav-badge { opacity: 1; }

        .sidebar:not(:hover) .nav-link:hover::after {
            content: attr(title);
            position: absolute; left: calc(var(--sidebar-w-sm) + 6px);
            top: 50%; transform: translateY(-50%);
            background: #0b1829; color: #e8f4ff;
            border: 1px solid rgba(77,168,247,.25);
            padding: 5px 11px; border-radius: 6px;
            font-size: 12px; white-space: nowrap;
            z-index: 200; pointer-events: none;
            box-shadow: 0 4px 12px rgba(0,0,0,.3);
        }

        .nav-link-painel {
            background: rgba(220,38,38,.18); color: #fca5a5;
            border-left-color: #ef4444;
            margin: 4px 6px; border-radius: 6px;
            padding: 8px 10px; justify-content: center;
        }
        .nav-link-painel:hover { background: rgba(220,38,38,.28); color: #fff; }
        .sidebar:hover .nav-link-painel { margin: 4px 8px; justify-content: flex-start; }

        /* ── Sidebar footer ── */
        .sidebar-footer { flex-shrink: 0; border-top: 1px solid rgba(255,255,255,.07); }
        .sidebar-user {
            padding: 10px 14px; font-size: 12px;
            color: rgba(255,255,255,.45);
            white-space: nowrap; overflow: hidden;
            display: flex; align-items: center; gap: 9px;
        }
        .sidebar-user-avatar {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, #1565e6, #2979ff);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }
        .sidebar-user-info { opacity: 0; width: 0; overflow: hidden; transition: opacity .18s, width .22s; }
        .sidebar:hover .sidebar-user-info { opacity: 1; width: auto; }
        .sidebar-user-info strong { display: block; color: rgba(255,255,255,.88); font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-info span   { font-size: 10px; color: rgba(255,255,255,.35); white-space: nowrap; }

        .sidebar-logout {
            display: block; width: 100%; padding: 8px 6px;
            background: none; border: none;
            border-top: 1px solid rgba(255,255,255,.06);
            color: rgba(255,255,255,.3); font-size: 11px;
            cursor: pointer; text-align: center;
            transition: color .12s, background .12s;
            font-family: inherit; white-space: nowrap; overflow: hidden;
        }
        .sidebar:hover .sidebar-logout { text-align: left; padding: 8px 14px; }
        .sidebar-logout:hover { color: #fca5a5; background: rgba(220,38,38,.1); }

        /* ── Main ── */
        .main-wrap { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
        .topbar {
            height: 54px; background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; padding: 0 22px; gap: 12px;
            flex-shrink: 0;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .page-body { flex: 1; overflow-y: auto; padding: 26px; }

        /* ── Topbar ── */
        .topbar-title { font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 14px; white-space: nowrap; color: var(--text); }
        .topbar-sep { color: var(--border-dark); }
        .topbar-sub { color: var(--text-2); font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .topbar-clock { font-family: 'IBM Plex Mono', monospace; font-size: 12px; color: var(--text-3); background: var(--bg); padding: 3px 9px; border-radius: 5px; border: 1px solid var(--border); }

        /* ── Hamburger (mobile only) ── */
        .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; flex-shrink: 0; }
        .hamburger span { display: block; width: 20px; height: 2px; background: var(--text-2); border-radius: 2px; }

        /* ── Overlay (mobile) ── */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 199; }
        .sidebar-overlay.open { display: block; }

        /* ── Alerts ── */
        .alert { padding: 10px 14px; border-radius: 7px; margin-bottom: 16px; font-size: 13px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .alert-danger  { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .alert-warn    { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }

        /* ── Cards & Metrics ── */
        .metric-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 24px; }
        .metric-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 10px; padding: 18px 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .metric-label { font-size: 11px; color: var(--text-3); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 8px; }
        .metric-value { font-family: 'IBM Plex Mono', monospace; font-size: 30px; font-weight: 600; color: var(--text); line-height: 1; }
        .metric-card.danger .metric-value { color: var(--danger); }
        .metric-card.warn   .metric-value { color: var(--warn); }
        .metric-card.ok     .metric-value { color: var(--ok); }

        /* ── Cards ── */
        .card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 10px; overflow: hidden; margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .card-header { padding: 14px 18px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; background: #fff; }
        .card-title { font-size: 14px; font-weight: 600; color: var(--text); }
        .card-actions { margin-left: auto; display: flex; gap: 8px; align-items: center; }

        /* ── Tables ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 9px 14px; text-align: left; font-size: 11px; font-weight: 600; color: var(--text-3); text-transform: uppercase; letter-spacing: .06em; border-bottom: 1px solid var(--border); background: #f8fafc; }
        td { padding: 11px 14px; border-bottom: 1px solid var(--border); font-size: 13px; vertical-align: middle; color: var(--text); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }

        /* ── Badges ── */
        .badge { display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 12px; font-size: 11px; font-weight: 600; letter-spacing: .02em; }
        .badge-rascunho  { background: #f1f5f9;  color: #64748b; }
        .badge-enviado   { background: #dbeafe;  color: #1d4ed8; }
        .badge-confirmado{ background: #e0e7ff;  color: #4338ca; }
        .badge-producao  { background: #fef3c7;  color: #b45309; }
        .badge-conferido { background: #d1fae5;  color: #065f46; }
        .badge-pronto    { background: #dcfce7;  color: #15803d; }
        .badge-atrasado  { background: #fee2e2;  color: #dc2626; }
        .badge-urgente   { background: #fee2e2;  color: #dc2626; }
        .badge-alta      { background: #fef3c7;  color: #b45309; }
        .badge-normal    { background: #f1f5f9;  color: #64748b; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 15px; border-radius: 7px; font-size: 13px; font-weight: 500; border: 1px solid transparent; cursor: pointer; text-decoration: none; transition: all .15s; font-family: inherit; }
        .btn:hover { opacity: .88; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .btn:active { transform: translateY(0); }
        .btn-primary   { background: linear-gradient(135deg, #1565e6, #2979ff); color: #fff; box-shadow: 0 2px 6px rgba(21,101,230,.3); }
        .btn-secondary { background: #fff; color: var(--text-2); border-color: var(--border-dark); }
        .btn-danger    { background: var(--danger); color: #fff; box-shadow: 0 2px 6px rgba(220,38,38,.25); }
        .btn-sm        { padding: 4px 11px; font-size: 12px; }

        /* ── Forms ── */
        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 12px; font-weight: 500; color: var(--text-2); margin-bottom: 5px; }
        .form-control {
            width: 100%; padding: 8px 11px;
            border: 1px solid var(--border-dark); border-radius: 7px;
            font-size: 13px; font-family: inherit;
            background: #fff; color: var(--text);
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control::placeholder { color: var(--text-3); }
        .form-control:focus { outline: none; border-color: #2979ff; box-shadow: 0 0 0 3px rgba(41,121,255,.1); }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; }
        .form-error { font-size: 11px; color: var(--danger); margin-top: 3px; }

        /* ── KDS ── */
        .kds-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .kds-col-header { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; padding: 8px 0; margin-bottom: 8px; border-bottom: 2px solid var(--border); color: var(--text-2); }
        .kds-card { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 14px; margin-bottom: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.05); }
        .kds-card.urgente { border-left: 3px solid #dc2626; }
        .kds-card.alta    { border-left: 3px solid #f59e0b; }

        /* ── Modals ── */
        .modal-backdrop { position: fixed; inset: 0; background: rgba(15,23,42,.45); z-index: 100; display: none; align-items: center; justify-content: center; }
        .modal-backdrop.open { display: flex; }
        .modal { background: #fff; border: 1px solid var(--border); border-radius: 12px; width: 95%; max-width: 580px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.18); }
        .modal-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; }
        .modal-title { font-size: 15px; font-weight: 600; color: var(--text); }
        .modal-close { margin-left: auto; background: none; border: none; cursor: pointer; font-size: 18px; color: var(--text-3); }
        .modal-body { padding: 20px; }
        .modal-footer { padding: 14px 20px; border-top: 1px solid var(--border); display: flex; gap: 8px; justify-content: flex-end; background: #f8fafc; border-radius: 0 0 12px 12px; }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 4px; margin-top: 16px; justify-content: center; }
        .pagination a, .pagination span { padding: 5px 10px; border: 1px solid var(--border); border-radius: 5px; font-size: 12px; text-decoration: none; color: var(--text-2); background: #fff; }
        .pagination a:hover { background: var(--accent-light); color: var(--accent); border-color: #bfdbfe; }
        .pagination .active span { background: var(--accent); color: #fff; border-color: var(--accent); }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .hamburger { display: flex; }
            .sidebar { position: fixed; left: -240px; top: 0; height: 100%; z-index: 200; transition: left .25s; width: 224px !important; }
            .sidebar.mobile-open { left: 0; }
            .sidebar.mobile-open .sidebar-brand-text,
            .sidebar.mobile-open .sidebar-user-info { opacity: 1 !important; width: auto !important; }
            .sidebar.mobile-open .sidebar-section { opacity: 1 !important; height: auto !important; padding: 10px 0 4px 14px !important; pointer-events: auto !important; }
            .sidebar.mobile-open .nav-label { opacity: 1 !important; width: auto !important; }
            .sidebar.mobile-open .nav-badge { opacity: 1 !important; }
            .sidebar.mobile-open .nav-link-painel { margin: 4px 8px !important; justify-content: flex-start !important; }
            .sidebar.mobile-open .sidebar-logout { text-align: left !important; padding: 8px 14px !important; }

            /* Topbar */
            .topbar { padding: 0 12px; height: 50px; }
            .topbar-clock { display: none; }
            .topbar-sub { display: none; }
            .topbar-title { font-size: 13px; }

            /* Conteúdo */
            .page-body { padding: 12px; }

            /* Formulários — 16px evita zoom no iOS */
            .form-control { font-size: 16px !important; padding: 10px 12px; }
            .form-row { grid-template-columns: 1fr !important; }
            .form-group { margin-bottom: 12px; }

            /* Botões touch-friendly */
            .btn { min-height: 42px; padding: 9px 14px; font-size: 13px; }
            .btn-sm { min-height: 34px; }

            /* Cards e métricas */
            .metric-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .metric-card { padding: 14px 12px; }
            .metric-value { font-size: 24px; }
            .card-header { padding: 12px 14px; }
            .card { margin-bottom: 14px; }

            /* Tabelas — scroll horizontal */
            .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            table { font-size: 12px; min-width: 480px; }
            td, th { padding: 8px 10px; }

            /* KDS */
            .kds-grid { grid-template-columns: 1fr !important; }
            .kds-card { padding: 12px; }

            /* Modais — ocupa mais tela */
            .modal { width: 98%; max-width: none; margin: 8px; border-radius: 14px; }
            .modal-body { padding: 16px; }
            .modal-footer { padding: 12px 16px; flex-direction: column; }
            .modal-footer .btn { width: 100%; justify-content: center; }

            /* Paginação */
            .pagination { flex-wrap: wrap; }
        }

        @media (max-width: 480px) {
            .metric-grid { grid-template-columns: 1fr 1fr; }
            .topbar-right { gap: 8px; }
            .btn-sm { padding: 6px 10px; font-size: 11px; }
        }

        /* ── Print ── */
        @media print {
            .sidebar, .topbar, .no-print { display: none !important; }
            .page-body { padding: 0; overflow: visible; }
            .main-wrap { overflow: visible; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeMobileSidebar()"></div>
<div class="app-shell">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-header">
            <div class="sidebar-logo">V</div>
            <div class="sidebar-brand-text">
                <div class="sidebar-brand">VORTEK</div>
                <div class="sidebar-sub">Controle de Produção</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            @php $u = auth()->user(); @endphp

            @if($u->temPermissao('dashboard') || $u->temPermissao('pedidos'))
            <div class="sidebar-section">Faturamento</div>
            @if($u->temPermissao('dashboard'))
            <a href="{{ route('dashboard') }}" class="nav-link @active('dashboard')" title="Dashboard">
                <span class="nav-icon">&#9632;</span>
                <span class="nav-label">Dashboard</span>
            </a>
            @endif
            @if($u->temPermissao('pedidos'))
            <a href="{{ route('pedidos.create') }}" class="nav-link @active('pedidos.create')" title="Novo Pedido">
                <span class="nav-icon">+</span>
                <span class="nav-label">Novo Pedido</span>
            </a>
            <a href="{{ route('pedidos.index') }}" class="nav-link @active('pedidos.index')" title="Pedidos">
                <span class="nav-icon">&#9783;</span>
                <span class="nav-label">Pedidos</span>
            </a>
            @endif
            @endif

            @if($u->temPermissao('painel') || $u->temPermissao('conferencia') || $u->temPermissao('kds'))
            <div class="sidebar-section">Produção</div>
            @if($u->temPermissao('painel'))
            <a href="{{ route('painel.index') }}" target="_blank"
               class="nav-link nav-link-painel" title="Painel Produção">
                <span class="nav-icon">&#9654;</span>
                <span class="nav-label">Painel Produção</span>
                @if($pendentesRecepcao ?? 0)
                    <span class="nav-badge">{{ $pendentesRecepcao }}</span>
                @endif
            </a>
            @endif
            @if($u->temPermissao('conferencia'))
            <a href="{{ route('producao.conferencia') }}" class="nav-link @active('producao.conferencia')" title="Conferência">
                <span class="nav-icon">&#10003;</span>
                <span class="nav-label">Conferência</span>
            </a>
            @endif
            @if($u->temPermissao('kds'))
            <a href="{{ route('producao.kds') }}" class="nav-link @active('producao.kds')" title="KDS Admin">
                <span class="nav-icon">&#9636;</span>
                <span class="nav-label">KDS Admin</span>
            </a>
            @endif
            @endif

            @if($u->temPermissao('calendario') || $u->temPermissao('relatorio'))
            <div class="sidebar-section">Rotas</div>
            @if($u->temPermissao('calendario'))
            <a href="{{ route('rotas.calendario') }}" class="nav-link @active('rotas.calendario')" title="Calendário">
                <span class="nav-icon">&#128197;</span>
                <span class="nav-label">Calendário</span>
            </a>
            @endif
            @if($u->temPermissao('relatorio'))
            <a href="{{ route('relatorio.index') }}" class="nav-link @active('relatorio.index')" title="Relatório">
                <span class="nav-icon">&#128202;</span>
                <span class="nav-label">Relatório</span>
            </a>
            @endif
            @endif

            @if($u->temPermissao('cadastros') || $u->isAdmin())
            <div class="sidebar-section">Cadastros</div>
            @if($u->temPermissao('cadastros'))
            <a href="{{ route('produtos.index') }}" class="nav-link @active('produtos.index')" title="Produtos">
                <span class="nav-icon">&#128230;</span>
                <span class="nav-label">Produtos</span>
            </a>
            <a href="{{ route('vendedores.index') }}" class="nav-link @active('vendedores.index')" title="Operadores">
                <span class="nav-icon">&#128100;</span>
                <span class="nav-label">Operadores</span>
            </a>
            <a href="{{ route('rotas.index') }}" class="nav-link @active('rotas.index')" title="Rotas">
                <span class="nav-icon">&#128652;</span>
                <span class="nav-label">Rotas</span>
            </a>
            @endif
            @if($u->isAdmin())
            <a href="{{ route('usuarios.index') }}" class="nav-link @active('usuarios.index')" title="Usuários">
                <span class="nav-icon">&#128273;</span>
                <span class="nav-label">Usuários</span>
            </a>
            @endif
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="sidebar-user-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-logout">
                    &#8594; Sair
                </button>
            </form>
        </div>

    </aside>

    <!-- Main -->
    <div class="main-wrap">
        <header class="topbar">
            <button class="hamburger" onclick="openMobileSidebar()" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
            @hasSection('page-subtitle')
                <span class="topbar-sep">—</span>
                <span class="topbar-sub">@yield('page-subtitle')</span>
            @endif
            <div class="topbar-right">
                <span class="topbar-clock" id="clock"></span>
                @yield('topbar-actions')
            </div>
        </header>

        <main class="page-body">
            @if(session('success'))
                <div class="alert alert-success">&#10003; {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">&#9888; {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0;padding-left:16px">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    // ── Mobile sidebar ──────────────────────────────────────────────────
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    function openMobileSidebar() {
        sidebar.classList.add('mobile-open');
        overlay.classList.add('open');
    }
    function closeMobileSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('open');
    }

    // ── Relógio ─────────────────────────────────────────────────────────
    function tick() {
        const now = new Date();
        const d = now.toLocaleDateString('pt-BR', {weekday:'short', day:'2-digit', month:'short'});
        const t = now.toLocaleTimeString('pt-BR', {hour:'2-digit', minute:'2-digit'});
        document.getElementById('clock').textContent = d + ' ' + t;
    }
    tick(); setInterval(tick, 1000);

    // ── Fechar alertas ────────────────────────────────────────────────
    document.querySelectorAll('.alert').forEach(el => {
        el.style.cursor = 'pointer';
        el.addEventListener('click', () => el.remove());
    });
</script>
@stack('scripts')
</body>
</html>
