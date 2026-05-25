<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoItemConferencia;
use Illuminate\Http\Request;

class ProducaoController extends Controller
{
    public function recepcao()
    {
        $pedidos = Pedido::with(['cliente', 'rota', 'itens.produto'])
            ->where('status', 'enviado')
            ->orderByRaw("FIELD(prioridade,'urgente','alta','normal')")
            ->orderBy('data_saida')
            ->get();

        return view('producao.recepcao', compact('pedidos'));
    }

    public function confirmar(Request $request, Pedido $pedido)
    {
        abort_if($pedido->status !== 'enviado', 422, 'Pedido não está aguardando confirmação.');

        $pedido->update([
            'status'        => 'confirmado',
            'confirmado_por'=> auth()->id(),
            'confirmado_em' => now(),
        ]);

        return back()->with('success', "Pedido #{$pedido->numero} confirmado pela produção.");
    }

    public function avancar(Pedido $pedido)
    {
        abort_if($pedido->status !== 'confirmado', 422);
        $pedido->update(['status' => 'producao']);
        return back()->with('success', "Pedido #{$pedido->numero} em produção.");
    }

    public function aceitar(Pedido $pedido)
    {
        abort_if(! in_array($pedido->status, ['enviado', 'confirmado']), 422);

        $pedido->update([
            'status'        => 'producao',
            'confirmado_por'=> auth()->id(),
            'confirmado_em' => now(),
        ]);

        return back()->with('success', "Pedido #{$pedido->numero} aceito e em produção.");
    }

    public function kds()
    {
        $pedidos = Pedido::with(['cliente', 'rota', 'itens.produto', 'confirmadoPorUser'])
            ->whereIn('status', ['enviado', 'confirmado', 'producao', 'pronto'])
            ->orderByRaw("FIELD(prioridade,'urgente','alta','normal')")
            ->orderBy('data_saida')
            ->get();

        $agrupados = [
            'pendente'  => $pedidos->whereIn('status', ['enviado', 'confirmado'])->values(),
            'producao'  => $pedidos->where('status', 'producao')->values(),
            'pronto'    => $pedidos->where('status', 'pronto')->values(),
        ];

        return view('producao.kds', compact('agrupados'));
    }

    public function conferencia()
    {
        $pedidos = Pedido::with(['cliente', 'itens.produto', 'itens.conferencia'])
            ->whereIn('status', ['producao', 'pronto'])
            ->orderByRaw("FIELD(prioridade,'urgente','alta','normal')")
            ->get();

        return view('producao.conferencia', compact('pedidos'));
    }

    public function finalizarConferencia(Request $request, Pedido $pedido)
    {
        $request->validate([
            'itens'                         => 'required|array',
            'itens.*.item_id'               => 'required|exists:pedido_itens,id',
            'itens.*.conferido'             => 'nullable|boolean',
            'itens.*.quantidade_conferida'  => 'nullable|numeric|min:0',
            'itens.*.observacoes'           => 'nullable|string',
            'observacoes_gerais'            => 'nullable|string',
        ]);

        foreach ($request->itens as $itemData) {
            PedidoItemConferencia::updateOrCreate(
                ['pedido_id' => $pedido->id, 'pedido_item_id' => $itemData['item_id']],
                [
                    'conferido'            => $itemData['conferido'] ?? false,
                    'quantidade_conferida' => $itemData['quantidade_conferida'] ?? null,
                    'observacoes'          => $itemData['observacoes'] ?? null,
                ]
            );
        }

        $pedido->update([
            'status'                  => 'pronto',
            'conferido_por'           => auth()->id(),
            'conferido_em'            => now(),
            'observacoes_conferencia' => $request->observacoes_gerais,
        ]);

        return back()->with('success', "Pedido #{$pedido->numero} conferido e marcado como Pronto para entrega.");
    }

    public function marcarPronto(Pedido $pedido)
    {
        abort_if(! in_array($pedido->status, ['conferido', 'producao']), 422);
        $pedido->update(['status' => 'pronto']);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', "Pedido #{$pedido->numero} marcado como pronto.");
    }
}
