<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Rota;
use App\Models\Vendedor;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['cliente', 'vendedor', 'rota']);

        if ($request->busca) {
            $query->where('cliente_nome', 'like', "%{$request->busca}%");
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->rota) {
            $query->where('rota_id', $request->rota);
        }
        if ($request->prioridade) {
            $query->where('prioridade', $request->prioridade);
        }

        $pedidos = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $rotas   = Rota::ativo()->orderBy('codigo')->get();

        return view('pedidos.index', compact('pedidos', 'rotas'));
    }

    public function create()
    {
        $vendedores = Vendedor::ativo()->orderBy('nome')->get();
        $rotas     = Rota::ativo()->orderBy('codigo')->get();
        $produtos  = Produto::ativo()->orderBy('categoria')->orderBy('nome')->get();

        return view('pedidos.create', compact('vendedores', 'rotas', 'produtos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_nome'       => 'required|string|max:255',
            'vendedor_id'        => 'required|exists:vendedores,id',
            'rota_id'            => 'nullable|exists:rotas,id',
            'data_saida'         => 'required|date',
            'prioridade'         => 'required|in:normal,alta,urgente',
            'observacoes'        => 'nullable|string',
            'itens'              => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.tipo'       => 'required|in:fracionado,kilo',
            'itens.*.quantidade' => 'required|numeric|min:0.001',
            'itens.*.unidade'    => 'required|in:kg,g,un,pct',
        ]);

        $pedido = Pedido::create([
            ...$data,
            'status'     => 'enviado',
            'criado_por' => auth()->id(),
        ]);

        foreach ($data['itens'] as $item) {
            $pedido->itens()->create($item);
        }

        return redirect()->route('pedidos.show', $pedido)
            ->with('success', "Pedido #{$pedido->numero} criado com sucesso.");
    }

    public function imprimir(Pedido $pedido)
    {
        $pedido->load(['cliente', 'vendedor', 'rota', 'itens.produto']);
        return view('pedidos.imprimir', compact('pedido'));
    }

    public function via(Pedido $pedido)
    {
        $pedido->load(['rota', 'itens.produto', 'criadoPorUser']);
        return view('pedidos.via', compact('pedido'));
    }

    public function cupom(Pedido $pedido)
    {
        $pedido->load(['cliente', 'vendedor', 'rota', 'itens.produto']);
        return view('pedidos.cupom', compact('pedido'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load([
            'cliente', 'vendedor', 'rota',
            'itens.produto', 'itens.conferencia',
            'criadoPorUser', 'confirmadoPorUser', 'conferidoPorUser',
        ]);

        return view('pedidos.show', compact('pedido'));
    }

    public function enviar(Pedido $pedido)
    {
        abort_if($pedido->status !== 'rascunho', 422, 'Pedido não pode ser enviado neste status.');
        $pedido->update(['status' => 'enviado']);
        return back()->with('success', "Pedido #{$pedido->numero} enviado para produção.");
    }

    public function destroy(Pedido $pedido)
    {
        if (! auth()->user()->temPermissao('pedidos_excluir')) {
            abort_if($pedido->status !== 'rascunho', 403, 'Sem permissão para excluir pedidos enviados.');
        }
        $numero = $pedido->numero;
        $pedido->delete();
        return redirect()->route('pedidos.index')->with('success', "Pedido #{$numero} excluído.");
    }
}
