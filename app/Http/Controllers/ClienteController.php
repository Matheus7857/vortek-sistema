<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Rota;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::with('rota');

        if ($request->busca) {
            $query->where('nome', 'like', "%{$request->busca}%");
        }
        if ($request->rota) {
            $query->where('rota_id', $request->rota);
        }
        if ($request->filled('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        $clientes = $query->orderBy('nome')->paginate(20)->withQueryString();
        $rotas    = Rota::ativo()->get();
        $metricas = [
            'total'  => Cliente::count(),
            'ativos' => Cliente::where('ativo', true)->count(),
        ];

        return view('clientes.index', compact('clientes', 'rotas', 'metricas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:255',
            'contato'     => 'nullable|string|max:255',
            'telefone'    => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:255',
            'cpf_cnpj'    => 'nullable|string|max:20',
            'endereco'    => 'nullable|string',
            'cidade'      => 'nullable|string|max:100',
            'rota_id'     => 'nullable|exists:rotas,id',
            'observacoes' => 'nullable|string',
        ]);

        Cliente::create($data);
        return back()->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:255',
            'contato'     => 'nullable|string|max:255',
            'telefone'    => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:255',
            'cpf_cnpj'    => 'nullable|string|max:20',
            'endereco'    => 'nullable|string',
            'cidade'      => 'nullable|string|max:100',
            'rota_id'     => 'nullable|exists:rotas,id',
            'observacoes' => 'nullable|string',
        ]);

        $cliente->update($data);
        return back()->with('success', 'Cliente atualizado.');
    }

    public function toggleAtivo(Cliente $cliente)
    {
        $cliente->update(['ativo' => ! $cliente->ativo]);
        $msg = $cliente->ativo ? 'Cliente ativado.' : 'Cliente desativado.';
        return back()->with('success', $msg);
    }
}
