@extends('layouts.app')

@section('page-title', 'Rotas')

@section('topbar-actions')
    <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-rota').classList.add('open')">+ Nova Rota</button>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Código</th><th>Nome</th><th>Motorista</th><th>Dias</th><th>Clientes Ativos</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($rotas as $r)
                        <tr>
                            <td><span style="font-family:'IBM Plex Mono',monospace;font-weight:600">{{ $r->codigo }}</span></td>
                            <td style="font-weight:500">{{ $r->nome }}</td>
                            <td>{{ $r->motorista ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--text-2)">{{ $r->dias_atendimento ?? '—' }}</td>
                            <td style="text-align:center">{{ $r->clientes_count }}</td>
                            <td>
                                <span class="badge" style="{{ $r->ativo ? 'background:var(--ok-light);color:var(--ok)' : 'background:#f1f0ed;color:var(--text-3)' }}">
                                    {{ $r->ativo ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td style="white-space:nowrap">
                                <button class="btn btn-secondary btn-sm" onclick='openEditRota(@json($r))'>Editar</button>
                                <form method="POST" action="{{ route('rotas.toggle', $r) }}" style="display:inline">
                                    @csrf
                                    <button class="btn btn-secondary btn-sm">{{ $r->ativo ? 'Desativar' : 'Ativar' }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:var(--text-3);padding:24px">Nenhuma rota cadastrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-backdrop" id="modal-rota" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title" id="modal-rota-title">Nova Rota</span>
                <button class="modal-close" onclick="document.getElementById('modal-rota').classList.remove('open')">&times;</button>
            </div>
            <form method="POST" id="form-rota" action="{{ route('rotas.store') }}">
                @csrf
                <input type="hidden" name="_method" id="rota-method" value="POST">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Código * (ex: R1)</label>
                            <input type="text" name="codigo" id="r-codigo" class="form-control" maxlength="10" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="nome" id="r-nome" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Motorista</label>
                            <input type="text" name="motorista" id="r-motorista" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telefone do Motorista</label>
                            <input type="text" name="telefone_motorista" id="r-telefone_motorista" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Região atendida</label>
                        <input type="text" name="regiao" id="r-regiao" class="form-control" placeholder="Bairros, cidades...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dias de atendimento</label>
                        <input type="text" name="dias_atendimento" id="r-dias_atendimento" class="form-control" placeholder="Seg, Qua, Sex">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-rota').classList.remove('open')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
function openEditRota(r) {
    document.getElementById('modal-rota-title').textContent = 'Editar Rota';
    document.getElementById('form-rota').action = `/cadastros/rotas/${r.id}`;
    document.getElementById('rota-method').value = 'PUT';
    ['codigo','nome','motorista','telefone_motorista','regiao','dias_atendimento'].forEach(f => {
        const el = document.getElementById('r-' + f);
        if (el) el.value = r[f] ?? '';
    });
    document.getElementById('modal-rota').classList.add('open');
}
</script>
@endpush
@endsection
