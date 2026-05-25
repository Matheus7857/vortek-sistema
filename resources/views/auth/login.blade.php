<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VORTEK — Sistema de Controle de Produção</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:       #020b14;
            --navy-2:     #040f1c;
            --navy-3:     #071525;
            --blue:       #1565e6;
            --blue-mid:   #2979ff;
            --blue-bright:#4da8f7;
            --blue-glow:  rgba(21,101,230,.25);
            --border:     rgba(77,168,247,.14);
            --border-focus: rgba(77,168,247,.55);
            --text:       #e8f4ff;
            --text-dim:   rgba(232,244,255,.42);
            --text-muted: rgba(232,244,255,.22);
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
        }

        /* ── Fundo global com ondas ── */
        .bg-waves {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        /* Orbs de brilho */
        .glow-orb {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Card principal ── */
        .login-wrap {
            position: relative;
            z-index: 1;
            display: flex;
            width: 100%;
            max-width: 920px;
            min-height: 560px;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow:
                0 0 0 1px rgba(77,168,247,.06),
                0 40px 80px rgba(0,0,0,.7),
                0 0 60px rgba(21,101,230,.08);
            animation: fadeUp .5s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px) scale(.98); }
            to   { opacity: 1; transform: translateY(0)     scale(1);  }
        }

        /* ── Painel esquerdo ── */
        .art-panel {
            flex: 1;
            background:
                radial-gradient(ellipse 70% 50% at 50% 100%, rgba(21,101,230,.18) 0%, transparent 65%),
                radial-gradient(ellipse 50% 40% at 100% 20%, rgba(41,121,255,.12) 0%, transparent 60%),
                linear-gradient(160deg, #030e1d 0%, #040f1c 60%, #030b18 100%);
            position: relative;
            overflow: hidden;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Grade de pontos */
        .art-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(77,168,247,.06) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* Linhas decorativas topo e base */
        .art-line {
            position: absolute;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(77,168,247,.2), transparent);
        }

        /* Brand */
        .art-brand { position: relative; z-index: 1; }
        .art-logo {
            font-family: 'JetBrains Mono', monospace;
            font-size: 32px;
            font-weight: 600;
            color: #fff;
            letter-spacing: -.01em;
        }
        .art-logo em {
            font-style: normal;
            background: linear-gradient(90deg, var(--blue-mid), var(--blue-bright));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .art-tagline {
            font-size: 11px;
            color: var(--text-dim);
            margin-top: 8px;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        /* Logo mark */
        .art-logo-wrap {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 0 20px;
        }
        .logo-company {
            font-family: 'JetBrains Mono', monospace;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-top: 14px;
            background: linear-gradient(90deg, #fff 30%, #4da8f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .logo-sub {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: rgba(232,244,255,.28);
            margin-top: 5px;
        }

        /* Features */
        .art-features { position: relative; z-index: 1; }
        .art-feature {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 11px;
        }
        .feat-icon {
            width: 26px; height: 26px;
            background: rgba(21,101,230,.18);
            border: 1px solid rgba(77,168,247,.2);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
        }
        .art-feature span { font-size: 12px; color: var(--text-dim); }

        /* ── Painel direito — formulário ── */
        .form-panel {
            width: 360px;
            flex-shrink: 0;
            background: linear-gradient(180deg, #061122 0%, #040d1a 100%);
            border-left: 1px solid var(--border);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        /* Linha brilhante no topo do form */
        .form-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 15%; right: 15%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--blue-mid), transparent);
            box-shadow: 0 0 8px rgba(41,121,255,.5);
        }

        .form-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 5px;
            letter-spacing: -.02em;
        }
        .form-sub {
            font-size: 13px;
            color: var(--text-dim);
            margin-bottom: 32px;
        }

        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: rgba(232,244,255,.38);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 7px;
        }
        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid rgba(77,168,247,.16);
            border-radius: 9px;
            font-size: 14px;
            font-family: inherit;
            background: rgba(255,255,255,.04);
            color: var(--text);
            transition: border-color .18s, box-shadow .18s, background .18s;
        }
        .form-control::placeholder { color: rgba(232,244,255,.2); }
        .form-control:focus {
            outline: none;
            border-color: var(--blue-mid);
            background: rgba(41,121,255,.06);
            box-shadow: 0 0 0 3px rgba(41,121,255,.1);
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-dim);
            cursor: pointer;
            margin-top: 4px;
        }
        .remember input { accent-color: var(--blue-mid); }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--blue) 0%, var(--blue-mid) 100%);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            margin-top: 22px;
            letter-spacing: .02em;
            transition: opacity .15s, transform .1s, box-shadow .2s;
            box-shadow: 0 4px 20px rgba(21,101,230,.35);
            position: relative;
            overflow: hidden;
        }
        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.12), transparent);
            transform: translateX(-100%);
            transition: transform .45s;
        }
        .btn-login:hover { opacity: .92; box-shadow: 0 6px 28px rgba(41,121,255,.45); }
        .btn-login:hover::after { transform: translateX(100%); }
        .btn-login:active { transform: scale(.985); }

        .error-msg {
            background: rgba(153,27,27,.15);
            color: #fca5a5;
            border: 1px solid rgba(252,165,165,.2);
            border-radius: 9px;
            padding: 11px 14px;
            font-size: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-footer {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid rgba(255,255,255,.05);
            font-size: 11px;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.7;
        }

        /* ── Responsivo ── */
        @media (max-width: 700px) {
            .art-panel  { display: none; }
            .login-wrap { max-width: 420px; border-radius: 16px; }
            .form-panel { width: 100%; padding: 36px 28px; }
        }
    </style>
</head>
<body>

{{-- Orbs de brilho de fundo --}}
<div class="glow-orb" style="width:600px;height:600px;bottom:-200px;left:-150px;background:radial-gradient(circle,rgba(21,101,230,.12) 0%,transparent 65%)"></div>
<div class="glow-orb" style="width:400px;height:400px;top:-100px;right:100px;background:radial-gradient(circle,rgba(41,121,255,.1) 0%,transparent 65%)"></div>
<div class="glow-orb" style="width:200px;height:200px;bottom:100px;right:50px;background:radial-gradient(circle,rgba(77,168,247,.08) 0%,transparent 70%)"></div>

{{-- Ondas SVG de fundo --}}
<svg class="bg-waves" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
    {{-- Cluster inferior esquerdo --}}
    <path d="M-80,820 Q120,700 300,740 Q480,780 620,680 Q760,580 900,620 Q1020,660 1100,580" stroke="rgba(41,121,255,.12)" stroke-width="1" fill="none"/>
    <path d="M-80,840 Q130,715 310,758 Q490,798 635,695 Q775,595 915,636 Q1038,676 1118,594" stroke="rgba(41,121,255,.10)" stroke-width="1" fill="none"/>
    <path d="M-80,858 Q140,730 320,774 Q500,816 648,710 Q790,608 930,650 Q1055,690 1136,606" stroke="rgba(41,121,255,.08)" stroke-width="1" fill="none"/>
    <path d="M-80,876 Q150,745 332,790 Q512,832 661,724 Q805,622 946,664 Q1070,704 1152,618" stroke="rgba(41,121,255,.06)" stroke-width="1" fill="none"/>
    <path d="M-80,790 Q110,670 285,712 Q465,754 602,654 Q742,554 880,596 Q1002,636 1082,554" stroke="rgba(77,168,247,.07)" stroke-width="1.2" fill="none"/>
    <path d="M-80,760 Q100,638 275,680 Q450,720 585,618 Q725,516 864,558 Q988,598 1066,516" stroke="rgba(77,168,247,.05)" stroke-width="1" fill="none"/>
    {{-- Cluster superior direito --}}
    <path d="M1520,80 Q1300,160 1140,110 Q980,60 860,160 Q740,260 620,210" stroke="rgba(41,121,255,.1)" stroke-width="1" fill="none"/>
    <path d="M1520,100 Q1310,182 1150,130 Q992,80 870,182 Q750,280 628,228" stroke="rgba(41,121,255,.08)" stroke-width="1" fill="none"/>
    <path d="M1520,120 Q1320,204 1162,150 Q1004,100 880,204 Q760,300 636,246" stroke="rgba(41,121,255,.06)" stroke-width="1" fill="none"/>
    {{-- Ponto de brilho --}}
    <circle cx="240" cy="650" r="2.5" fill="rgba(77,168,247,.7)">
        <animate attributeName="opacity" values="0.7;0.2;0.7" dur="3s" repeatCount="indefinite"/>
    </circle>
    <circle cx="1200" cy="200" r="2" fill="rgba(77,168,247,.6)">
        <animate attributeName="opacity" values="0.6;0.15;0.6" dur="4s" repeatCount="indefinite"/>
    </circle>
    {{-- Grade de pontos cantos --}}
    <g opacity=".35">
        @for($r = 0; $r < 5; $r++)
            @for($c = 0; $c < 6; $c++)
                <circle cx="{{ 30 + $c * 22 }}" cy="{{ 30 + $r * 22 }}" r="1.2" fill="rgba(77,168,247,.3)"/>
            @endfor
        @endfor
    </g>
    <g opacity=".3">
        @for($r = 0; $r < 4; $r++)
            @for($c = 0; $c < 5; $c++)
                <circle cx="{{ 1310 + $c * 22 }}" cy="{{ 600 + $r * 22 }}" r="1.2" fill="rgba(77,168,247,.3)"/>
            @endfor
        @endfor
    </g>
</svg>

<div class="login-wrap">

    {{-- ── Painel de Arte ── --}}
    <div class="art-panel">

        {{-- Linhas decorativas --}}
        <div class="art-line" style="top:30%;left:0;right:0;opacity:.6"></div>
        <div class="art-line" style="top:70%;left:0;right:0;opacity:.4"></div>

        {{-- Brilho central suave --}}
        <div style="position:absolute;width:300px;height:300px;bottom:-80px;left:50%;transform:translateX(-50%);background:radial-gradient(circle,rgba(21,101,230,.14) 0%,transparent 70%);pointer-events:none"></div>

        {{-- Logo VORTEK V 3D --}}
        <div class="art-logo-wrap">
            <svg width="210" height="185" viewBox="0 0 210 185" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    {{-- Gradiente azul para o braço esquerdo --}}
                    <linearGradient id="vBlue" x1="8" y1="8" x2="105" y2="175" gradientUnits="userSpaceOnUse">
                        <stop offset="0%"   stop-color="#62d4ff"/>
                        <stop offset="38%"  stop-color="#1b6de8"/>
                        <stop offset="100%" stop-color="#061840"/>
                    </linearGradient>
                    <linearGradient id="vBlueBevel" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%"   stop-color="#90e0ff"/>
                        <stop offset="100%" stop-color="#3090ff"/>
                    </linearGradient>
                    {{-- Gradiente prata/chrome para o braço direito --}}
                    <linearGradient id="vSilver" x1="202" y1="8" x2="105" y2="175" gradientUnits="userSpaceOnUse">
                        <stop offset="0%"   stop-color="#ffffff"/>
                        <stop offset="18%"  stop-color="#c8dcf0"/>
                        <stop offset="62%"  stop-color="#5272a0"/>
                        <stop offset="100%" stop-color="#0c1a2e"/>
                    </linearGradient>
                    <linearGradient id="vSilverBevel" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%"   stop-color="#3090ff"/>
                        <stop offset="100%" stop-color="#e8f4ff"/>
                    </linearGradient>
                    {{-- Glow suave --}}
                    <filter id="vGlow" x="-15%" y="-10%" width="130%" height="120%">
                        <feGaussianBlur stdDeviation="4" result="b"/>
                        <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
                    </filter>
                    <filter id="vOuterGlow" x="-40%" y="-30%" width="180%" height="160%">
                        <feGaussianBlur stdDeviation="12"/>
                    </filter>
                </defs>

                {{-- Halo de brilho atrás do V --}}
                <ellipse cx="105" cy="100" rx="82" ry="68"
                    fill="#1565e6" fill-opacity=".18"
                    filter="url(#vOuterGlow)"/>

                {{-- ── BRAÇO ESQUERDO (azul) ── --}}
                {{-- Face principal --}}
                <polygon points="8,10 74,10 85,26 105,172"
                    fill="url(#vBlue)"
                    filter="url(#vGlow)"/>
                {{-- Bevel superior (highlight) --}}
                <polygon points="8,10 74,10 78,3 12,3"
                    fill="url(#vBlueBevel)" opacity=".88"/>
                {{-- Face do entalhe (mostra profundidade 3D) --}}
                <polygon points="74,10 85,26 80,28 69,14"
                    fill="#071e5a" opacity=".8"/>
                {{-- Aresta externa com brilho --}}
                <line x1="8" y1="10" x2="105" y2="172"
                    stroke="#4dc8ff" stroke-width="1.4" opacity=".55"/>
                {{-- Stripe de luz na face --}}
                <polygon points="8,10 28,10 75,155 58,172"
                    fill="rgba(255,255,255,.06)"/>

                {{-- ── BRAÇO DIREITO (prata/chrome) ── --}}
                {{-- Face principal --}}
                <polygon points="202,10 136,10 125,26 105,172"
                    fill="url(#vSilver)"/>
                {{-- Bevel superior (highlight) --}}
                <polygon points="202,10 136,10 132,3 198,3"
                    fill="url(#vSilverBevel)" opacity=".85"/>
                {{-- Face do entalhe --}}
                <polygon points="136,10 125,26 130,28 141,14"
                    fill="#111e30" opacity=".75"/>
                {{-- Aresta externa com brilho --}}
                <line x1="202" y1="10" x2="105" y2="172"
                    stroke="#d0e8ff" stroke-width="1.8" opacity=".55"/>
                {{-- Stripe de luz na face direita --}}
                <polygon points="202,10 182,10 135,155 152,172"
                    fill="rgba(255,255,255,.08)"/>

                {{-- Ponto de encontro (tip) --}}
                <circle cx="105" cy="172" r="4" fill="rgba(255,255,255,.35)"/>
                <circle cx="105" cy="172" r="2" fill="rgba(255,255,255,.7)"/>

                {{-- Accent dots nos cantos superiores --}}
                <circle cx="8"   cy="10" r="3"   fill="#62d4ff" opacity=".95" filter="url(#vGlow)"/>
                <circle cx="74"  cy="10" r="2"   fill="#62d4ff" opacity=".65"/>
                <circle cx="136" cy="10" r="2"   fill="#d0e8ff" opacity=".65"/>
                <circle cx="202" cy="10" r="3"   fill="#ffffff"  opacity=".95"/>

                {{-- Linha de brilho no topo conectando os dois braços --}}
                <line x1="8" y1="3" x2="74" y2="3" stroke="#62d4ff" stroke-width=".8" opacity=".4"/>
                <line x1="136" y1="3" x2="202" y2="3" stroke="#ffffff" stroke-width=".8" opacity=".4"/>
            </svg>
            <div class="logo-company">VORTEK</div>
            <div class="logo-sub">Sistema de Controle de Produção</div>
        </div>

        {{-- Features --}}
        <div class="art-features">
            <div class="art-feature">
                <div class="feat-icon">&#9783;</div>
                <span>Gestão completa de pedidos</span>
            </div>
            <div class="art-feature">
                <div class="feat-icon">&#9881;</div>
                <span>Controle de produção em tempo real</span>
            </div>
            <div class="art-feature">
                <div class="feat-icon">&#128652;</div>
                <span>Rotas e calendário de entregas</span>
            </div>
            <div class="art-feature">
                <div class="feat-icon">&#128202;</div>
                <span>Relatórios e rastreabilidade</span>
            </div>
        </div>

    </div>

    {{-- ── Painel do Formulário ── --}}
    <div class="form-panel">

        <div class="form-title">Entrar</div>
        <div class="form-sub">Acesse o sistema com suas credenciais</div>

        @if($errors->any())
            <div class="error-msg">
                <span>&#9888;</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email') }}" required autofocus
                       placeholder="seu@email.com">
            </div>
            <div class="form-group">
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-control"
                       required placeholder="••••••••">
            </div>
            <label class="remember">
                <input type="checkbox" name="remember"> Manter conectado
            </label>
            <button type="submit" class="btn-login">
                Acessar o Sistema &nbsp;&#8594;
            </button>
        </form>

        <div class="form-footer">
            Acesso restrito ao sistema<br>
            Use as credenciais fornecidas pelo administrador
        </div>

    </div>

</div>

</body>
</html>
