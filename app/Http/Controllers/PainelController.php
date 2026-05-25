<?php

namespace App\Http\Controllers;

use App\Models\Pedido;

class PainelController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with(['cliente', 'rota', 'itens.produto'])
            ->where(function ($q) {
                $q->whereIn('status', ['enviado', 'producao'])
                  ->orWhere(function ($q2) {
                      // Prontos só aparecem se data_saida for hoje ou futura
                      $q2->where('status', 'pronto')
                         ->whereDate('data_saida', '>=', today());
                  });
            })
            ->orderByRaw("FIELD(prioridade,'urgente','alta','normal')")
            ->orderBy('data_saida')
            ->get();

        return view('painel.index', [
            'pendentes' => $pedidos->where('status', 'enviado')->values(),
            'producao'  => $pedidos->where('status', 'producao')->values(),
            'prontos'   => $pedidos->where('status', 'pronto')->values(),
        ]);
    }

    public function aceitar(Pedido $pedido)
    {
        abort_if($pedido->status !== 'enviado', 422);

        $pedido->update([
            'status'        => 'producao',
            'confirmado_em' => now(),
        ]);

        return back();
    }

    public function pronto(Pedido $pedido)
    {
        abort_if($pedido->status !== 'producao', 422);
        $pedido->update(['status' => 'pronto']);
        return back();
    }

    public function voltarPendente(Pedido $pedido)
    {
        abort_if($pedido->status !== 'producao', 422);
        $pedido->update([
            'status'        => 'enviado',
            'confirmado_em' => null,
            'confirmado_por'=> null,
        ]);
        return back();
    }

    public function voltarProducao(Pedido $pedido)
    {
        abort_if($pedido->status !== 'pronto', 422);
        $pedido->update(['status' => 'producao']);
        return back();
    }
}
