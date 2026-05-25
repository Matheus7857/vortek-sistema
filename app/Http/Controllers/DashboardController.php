<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoje = Carbon::today();

        $metricas = [
            'total'         => Pedido::count(),
            'nao_confirmados' => Pedido::where('status', 'enviado')->count(),
            'em_producao'   => Pedido::whereIn('status', ['confirmado', 'producao'])->count(),
            'atrasados'     => Pedido::where('data_saida', '<', $hoje)
                                     ->whereNotIn('status', ['pronto', 'conferido'])->count(),
            'prontos_hoje'  => Pedido::whereDate('data_saida', $hoje)->where('status', 'pronto')->count(),
        ];

        $pedidos_recentes = Pedido::with(['cliente', 'rota'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact('metricas', 'pedidos_recentes'));
    }
}
