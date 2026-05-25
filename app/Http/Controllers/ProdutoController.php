<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $query = Produto::query();

        if ($request->busca) {
            $query->where('nome', 'like', "%{$request->busca}%");
        }
        if ($request->categoria) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        $produtos = $query->orderBy('categoria')->orderBy('nome')->paginate(20)->withQueryString();
        $metricas = [
            'total'     => Produto::count(),
            'ativos'    => Produto::where('ativo', true)->count(),
            'embutidos' => Produto::where('categoria', 'embutido')->where('ativo', true)->count(),
            'queijos'   => Produto::where('categoria', 'queijo')->where('ativo', true)->count(),
        ];

        return view('produtos.index', compact('produtos', 'metricas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:255',
            'categoria'   => 'required|in:embutido,queijo,outro',
            'tipo_padrao' => 'required|in:fracionado,kilo',
            'unidade'     => 'required|in:kg,g,un,pct',
            'peso_minimo' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
        ]);

        Produto::create($data);
        return back()->with('success', 'Produto cadastrado.');
    }

    public function update(Request $request, Produto $produto)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:255',
            'categoria'   => 'required|in:embutido,queijo,outro',
            'tipo_padrao' => 'required|in:fracionado,kilo',
            'unidade'     => 'required|in:kg,g,un,pct',
            'peso_minimo' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
        ]);

        $produto->update($data);
        return back()->with('success', 'Produto atualizado.');
    }

    public function toggleAtivo(Produto $produto)
    {
        $produto->update(['ativo' => ! $produto->ativo]);
        return back()->with('success', $produto->ativo ? 'Produto ativado.' : 'Produto desativado.');
    }
}
