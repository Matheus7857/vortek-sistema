@extends('layouts.app')

@section('page-title', 'Usuários')

@section('topbar-actions')
    <button onclick="document.getElementById('modal-novo').classList.add('open')"
            class="btn btn-primary btn-sm no-print">+ Novo Usuário</button>
@endsection

@section('content')

@php
$todasPermissoes = [
    ['key' => 'dashboard',       'label' => 'Dashboard'],
    ['key' => 'pedidos',         'label' => 'Criar / Ver Pedidos'],
    ['key' => 'pedidos_excluir', 'label' => 'Excluir qualquer pedido'],
    ['key' => 'painel',          'label' => 'Painel de Produção'],
    ['key' => 'kds',             'label' => 'KDS Admin'],
    ['key' => 'conferencia',     'label' => 'Conferência'],
    ['key' => 'relatorio',       'label' => 'Relatório'],
    ['key' => 'calendario',      'label' => 'Calendário de Rotas'],
    ['key' => 'cadastros',       'label' => 'Produtos / Operadores / Rotas'],
];
@endphp

<div class="card">
    <div class="card-header">
        <span class="card-title">Usuários do Sistema</span>
        <span style="margin-left:auto;font-size:12px;color:var(--text-3)">{{ $users->count() }} usuário(s)</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div style="width:30px;height:30px;border-radius:50%;background:var(--accent-light);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:500">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span style="font-size:10px;color:var(--text-3)">(você)</span>
                                        @endif
                                    </div>
                                    @if($user->role !== 'admin' && $user->permissions)
                                        <div style="font-size:11px;color:var(--text-3);margin-top:2px">
                                            {{ count($user->permissions) }} permiss{{ count($user->permissions) === 1 ? 'ão' : 'ões' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-2)">{{ $user->email }}</td>
                        <td>
                            @php
                                $roleLabel = match($user->role) {
                                    'admin'       => 'Administrador',
                                    'faturamento' => 'Faturamento',
                                    'producao'    => 'Produção',
                                    default       => $user->role,
                                };
                                $roleColor = match($user->role) {
                                    'admin'       => 'badge-urgente',
                                    'faturamento' => 'badge-enviado',
                                    'producao'    => 'badge-producao',
                                    default       => 'badge-normal',
                                };
                            @endphp
                            <span class="badge {{ $roleColor }}">{{ $roleLabel }}</span>
                        </td>
                        <td>
                            @if($user->ativo)
                                <span style="color:var(--ok);font-size:12px;font-weight:600">&#10003; Ativo</span>
                            @else
                                <span style="color:var(--text-3);font-size:12px">&#10005; Inativo</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            <button onclick="abrirEditar({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->role }}', {{ json_encode($user->permissions ?? []) }})"
                                    class="btn btn-secondary btn-sm">Editar</button>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('usuarios.toggle', $user) }}" style="display:inline"
                                      onsubmit="return confirm('{{ $user->ativo ? 'Desativar' : 'Ativar' }} usuário {{ $user->name }}?')">
                                    @csrf
                                    <button class="btn btn-sm" style="background:{{ $user->ativo ? '#fef2f2' : '#f0fdf4' }};color:{{ $user->ativo ? 'var(--danger)' : 'var(--ok)' }};border:1px solid {{ $user->ativo ? '#fecaca' : '#bbf7d0' }}">
                                        {{ $user->ativo ? 'Desativar' : 'Ativar' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Novo Usuário --}}
<div class="modal-backdrop" id="modal-novo">
    <div class="modal" style="max-width:560px">
        <div class="modal-header">
            <span class="modal-title">Novo Usuário</span>
            <button class="modal-close" onclick="document.getElementById('modal-novo').classList.remove('open')">&times;</button>
        </div>
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">E-mail *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        @error('email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Senha *</label>
                        <input type="password" name="password" class="form-control" required autocomplete="new-password">
                        @error('password')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmar Senha *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Perfil *</label>
                    <select name="role" id="novo-role" class="form-control" required
                            onchange="onRoleChange(this.value, 'novo')">
                        <option value="faturamento" @selected(old('role','faturamento')=='faturamento')>Faturamento</option>
                        <option value="producao"    @selected(old('role')=='producao')>Produção</option>
                        <option value="admin"       @selected(old('role')=='admin')>Administrador</option>
                    </select>
                </div>

                {{-- Permissões --}}
                <div id="perms-box-novo">
                    <div style="margin-bottom:6px;font-size:12px;font-weight:600;color:var(--text-2)">Permissões de acesso</div>
                    <div id="admin-note-novo" style="display:none;padding:8px 12px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;font-size:12px;color:#991b1b">
                        Administrador tem acesso completo a todas as funções do sistema.
                    </div>
                    <div id="perms-checks-novo" class="perms-grid">
                        @foreach($todasPermissoes as $p)
                        <label class="perm-check">
                            <input type="checkbox" name="permissions[]" value="{{ $p['key'] }}"
                                   @if(in_array($p['key'], old('permissions', []))) checked @endif>
                            <span>{{ $p['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-row" style="margin-top:0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        onclick="document.getElementById('modal-novo').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Criar Usuário</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Editar Usuário --}}
<div class="modal-backdrop" id="modal-editar">
    <div class="modal" style="max-width:560px">
        <div class="modal-header">
            <span class="modal-title">Editar Usuário</span>
            <button class="modal-close" onclick="document.getElementById('modal-editar').classList.remove('open')">&times;</button>
        </div>
        <form method="POST" id="form-editar">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">E-mail *</label>
                        <input type="email" name="email" id="edit-email" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Perfil *</label>
                    <select name="role" id="edit-role" class="form-control" required
                            onchange="onRoleChange(this.value, 'edit')">
                        <option value="faturamento">Faturamento</option>
                        <option value="producao">Produção</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>

                {{-- Permissões --}}
                <div id="perms-box-edit">
                    <div style="margin-bottom:6px;font-size:12px;font-weight:600;color:var(--text-2)">Permissões de acesso</div>
                    <div id="admin-note-edit" style="display:none;padding:8px 12px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;font-size:12px;color:#991b1b">
                        Administrador tem acesso completo a todas as funções do sistema.
                    </div>
                    <div id="perms-checks-edit" class="perms-grid">
                        @foreach($todasPermissoes as $p)
                        <label class="perm-check">
                            <input type="checkbox" name="permissions[]" value="{{ $p['key'] }}">
                            <span>{{ $p['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-row" style="margin-top:12px">
                    <div class="form-group">
                        <label class="form-label">Nova Senha <span style="color:var(--text-3);font-weight:400">(deixe em branco para manter)</span></label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmar Nova Senha</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        onclick="document.getElementById('modal-editar').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<style>
.perms-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    padding: 10px 12px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 6px;
}
.perm-check {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    cursor: pointer;
    padding: 3px 4px;
    border-radius: 4px;
}
.perm-check:hover { background: var(--hover); }
.perm-check input[type=checkbox] { width:15px;height:15px;cursor:pointer;flex-shrink:0; }
@media(max-width:480px){
    .perms-grid { grid-template-columns: 1fr; }
}
</style>
<script>
    const padroesPorPerfil = {
        admin:       null,
        faturamento: ['dashboard','pedidos','relatorio','calendario','cadastros'],
        producao:    ['painel','kds','conferencia'],
    };

    function onRoleChange(role, prefix) {
        const adminNote  = document.getElementById('admin-note-' + prefix);
        const checksBox  = document.getElementById('perms-checks-' + prefix);

        if (role === 'admin') {
            adminNote.style.display = 'block';
            checksBox.style.display = 'none';
        } else {
            adminNote.style.display = 'none';
            checksBox.style.display = 'grid';
            aplicarPadrao(role, prefix);
        }
    }

    function aplicarPadrao(role, prefix) {
        const padrao = padroesPorPerfil[role] || [];
        document.querySelectorAll(`#perms-checks-${prefix} input[type=checkbox]`).forEach(cb => {
            cb.checked = padrao.includes(cb.value);
        });
    }

    function marcarPermissoes(prefix, perms) {
        document.querySelectorAll(`#perms-checks-${prefix} input[type=checkbox]`).forEach(cb => {
            cb.checked = (perms || []).includes(cb.value);
        });
    }

    function abrirEditar(id, name, email, role, permissions) {
        document.getElementById('edit-name').value  = name;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-role').value  = role;
        document.getElementById('form-editar').action = `/cadastros/usuarios/${id}`;

        const adminNote = document.getElementById('admin-note-edit');
        const checksBox = document.getElementById('perms-checks-edit');

        if (role === 'admin') {
            adminNote.style.display = 'block';
            checksBox.style.display = 'none';
        } else {
            adminNote.style.display = 'none';
            checksBox.style.display = 'grid';
            marcarPermissoes('edit', permissions);
        }

        document.getElementById('modal-editar').classList.add('open');
    }

    // Inicializar modal novo com perfil padrão (faturamento)
    onRoleChange('{{ old('role', 'faturamento') }}', 'novo');

    @if(old('permissions'))
        // Restaurar checkboxes após erro de validação
        marcarPermissoes('novo', @json(old('permissions', [])));
    @endif

    @if($errors->any())
        document.getElementById('modal-novo').classList.add('open');
    @endif
</script>
@endpush

@endsection
