<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Rota;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function index(Request $request)
    {
        $data  = $request->data  ?? today()->format('Y-m-d');
        $rotaId = $request->rota ?? null;

        $query = Pedido::with(['rota', 'itens.produto', 'vendedor'])
            ->whereIn('status', ['enviado', 'producao', 'conferido', 'pronto'])
            ->whereDate('data_saida', $data)
            ->orderByRaw("FIELD(prioridade,'urgente','alta','normal')");

        if ($rotaId) {
            $query->where('rota_id', $rotaId);
        }

        $pedidos = $query->get();
        $rotas   = Rota::ativo()->orderBy('codigo')->get();

        // Consolidar quantidades por produto
        $totais = $pedidos
            ->flatMap(fn ($p) => $p->itens)
            ->groupBy('produto_id')
            ->map(fn ($itens) => [
                'nome'     => $itens->first()->produto->nome,
                'unidade'  => $itens->first()->unidade,
                'total'    => $itens->sum('quantidade'),
            ])
            ->sortBy('nome')
            ->values();

        return view('relatorio.index', compact('pedidos', 'rotas', 'totais', 'data', 'rotaId'));
    }
}
