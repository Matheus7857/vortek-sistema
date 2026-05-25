<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Rota;
use Illuminate\Http\Request;

class RotaController extends Controller
{
    public function index()
    {
        $rotas = Rota::withCount(['clientes' => fn ($q) => $q->where('ativo', true)])->get();
        return view('rotas.index', compact('rotas'));
    }

    public function calendario()
    {
        $pedidos = Pedido::with(['cliente', 'rota', 'itens'])
            ->whereNotIn('status', ['pronto'])
            ->orderBy('data_saida')
            ->orderByRaw("FIELD(prioridade,'urgente','alta','normal')")
            ->get()
            ->groupBy(fn ($p) => $p->data_saida->format('Y-m-d'));

        return view('rotas.calendario', compact('pedidos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo'             => 'required|string|max:10|unique:rotas',
            'nome'               => 'required|string|max:255',
            'motorista'          => 'nullable|string|max:255',
            'telefone_motorista' => 'nullable|string|max:20',
            'regiao'             => 'nullable|string',
            'dias_atendimento'   => 'nullable|string',
        ]);

        Rota::create($data);
        return back()->with('success', 'Rota cadastrada.');
    }

    public function update(Request $request, Rota $rota)
    {
        $data = $request->validate([
            'codigo'             => 'required|string|max:10|unique:rotas,codigo,' . $rota->id,
            'nome'               => 'required|string|max:255',
            'motorista'          => 'nullable|string|max:255',
            'telefone_motorista' => 'nullable|string|max:20',
            'regiao'             => 'nullable|string',
            'dias_atendimento'   => 'nullable|string',
        ]);

        $rota->update($data);
        return back()->with('success', 'Rota atualizada.');
    }

    public function toggleAtivo(Rota $rota)
    {
        $rota->update(['ativo' => ! $rota->ativo]);
        return back();
    }
}
