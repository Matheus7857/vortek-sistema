<?php

namespace App\Http\Controllers;

use App\Models\Vendedor;
use Illuminate\Http\Request;

class VendedorController extends Controller
{
    public function index()
    {
        $vendedores = Vendedor::orderBy('setor')->orderBy('nome')->get();
        return view('vendedores.index', compact('vendedores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'     => 'required|string|max:255',
            'setor'    => 'required|in:faturamento,vendas,producao,administrativo',
            'telefone' => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
        ]);

        Vendedor::create($data);
        return back()->with('success', 'Operador cadastrado.');
    }

    public function update(Request $request, Vendedor $vendedor)
    {
        $data = $request->validate([
            'nome'     => 'required|string|max:255',
            'setor'    => 'required|in:faturamento,vendas,producao,administrativo',
            'telefone' => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
        ]);

        $vendedor->update($data);
        return back()->with('success', 'Operador atualizado.');
    }

    public function toggleAtivo(Vendedor $vendedor)
    {
        $vendedor->update(['ativo' => ! $vendedor->ativo]);
        return back();
    }
}
