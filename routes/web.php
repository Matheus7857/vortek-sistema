<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PainelController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProducaoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\RotaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendedorController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// Autenticação
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

// ─── Painel de Produção (sem login) ───────────────────────────────────────────
Route::get('/painel',                   [PainelController::class, 'index'] )->name('painel.index');
Route::post('/painel/{pedido}/aceitar',         [PainelController::class, 'aceitar']       )->name('painel.aceitar');
Route::post('/painel/{pedido}/pronto',          [PainelController::class, 'pronto']        )->name('painel.pronto');
Route::post('/painel/{pedido}/voltar-pendente', [PainelController::class, 'voltarPendente'])->name('painel.voltar-pendente');
Route::post('/painel/{pedido}/voltar-producao', [PainelController::class, 'voltarProducao'])->name('painel.voltar-producao');

// Cupom acessível sem login (para abrir na impressora)
Route::get('/pedidos/{pedido}/cupom', [PedidoController::class, 'cupom'])->name('pedidos.cupom');

// ─── Sistema administrativo (requer login) ────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pedidos
    Route::get('/pedidos',                   [PedidoController::class, 'index']  )->name('pedidos.index');
    Route::get('/pedidos/novo',              [PedidoController::class, 'create'] )->name('pedidos.create');
    Route::post('/pedidos',                  [PedidoController::class, 'store']  )->name('pedidos.store');
    Route::get('/pedidos/{pedido}',          [PedidoController::class, 'show']   )->name('pedidos.show');
    Route::get('/pedidos/{pedido}/imprimir', [PedidoController::class, 'imprimir'])->name('pedidos.imprimir');
    Route::get('/pedidos/{pedido}/via',     [PedidoController::class, 'via']     )->name('pedidos.via');
    Route::post('/pedidos/{pedido}/enviar',  [PedidoController::class, 'enviar'] )->name('pedidos.enviar');
    Route::delete('/pedidos/{pedido}',       [PedidoController::class, 'destroy'])->name('pedidos.destroy');

    // Produção (visão administrativa)
    Route::get('/producao/recepcao',                [ProducaoController::class, 'recepcao']            )->name('producao.recepcao');
    Route::post('/producao/{pedido}/confirmar',     [ProducaoController::class, 'confirmar']           )->name('producao.confirmar');
    Route::post('/producao/{pedido}/aceitar',       [ProducaoController::class, 'aceitar']             )->name('producao.aceitar');
    Route::get('/producao/kds',                     [ProducaoController::class, 'kds']                 )->name('producao.kds');
    Route::get('/producao/conferencia',             [ProducaoController::class, 'conferencia']         )->name('producao.conferencia');
    Route::post('/producao/{pedido}/conferencia',   [ProducaoController::class, 'finalizarConferencia'])->name('producao.finalizar-conferencia');
    Route::post('/producao/{pedido}/pronto',        [ProducaoController::class, 'marcarPronto']        )->name('producao.pronto');

    // Relatório
    Route::get('/relatorio', [RelatorioController::class, 'index'])->name('relatorio.index');

    // Calendário
    Route::get('/rotas/calendario', [RotaController::class, 'calendario'])->name('rotas.calendario');

    // Cadastros — Clientes
    Route::get('/cadastros/clientes',                   [ClienteController::class, 'index']      )->name('clientes.index');
    Route::post('/cadastros/clientes',                  [ClienteController::class, 'store']      )->name('clientes.store');
    Route::put('/cadastros/clientes/{cliente}',         [ClienteController::class, 'update']     )->name('clientes.update');
    Route::post('/cadastros/clientes/{cliente}/toggle', [ClienteController::class, 'toggleAtivo'])->name('clientes.toggle');

    // Cadastros — Produtos
    Route::get('/cadastros/produtos',                   [ProdutoController::class, 'index']      )->name('produtos.index');
    Route::post('/cadastros/produtos',                  [ProdutoController::class, 'store']      )->name('produtos.store');
    Route::put('/cadastros/produtos/{produto}',         [ProdutoController::class, 'update']     )->name('produtos.update');
    Route::post('/cadastros/produtos/{produto}/toggle', [ProdutoController::class, 'toggleAtivo'])->name('produtos.toggle');

    // Cadastros — Vendedores
    Route::get('/cadastros/vendedores',                    [VendedorController::class, 'index']      )->name('vendedores.index');
    Route::post('/cadastros/vendedores',                   [VendedorController::class, 'store']      )->name('vendedores.store');
    Route::put('/cadastros/vendedores/{vendedor}',         [VendedorController::class, 'update']     )->name('vendedores.update');
    Route::post('/cadastros/vendedores/{vendedor}/toggle', [VendedorController::class, 'toggleAtivo'])->name('vendedores.toggle');

    // Cadastros — Rotas
    Route::get('/cadastros/rotas',                    [RotaController::class, 'index']      )->name('rotas.index');
    Route::post('/cadastros/rotas',                   [RotaController::class, 'store']      )->name('rotas.store');
    Route::put('/cadastros/rotas/{rota}',             [RotaController::class, 'update']     )->name('rotas.update');
    Route::post('/cadastros/rotas/{rota}/toggle',     [RotaController::class, 'toggleAtivo'])->name('rotas.toggle');

    // Cadastros — Usuários
    Route::get('/cadastros/usuarios',                     [UserController::class, 'index']     )->name('usuarios.index');
    Route::post('/cadastros/usuarios',                    [UserController::class, 'store']     )->name('usuarios.store');
    Route::put('/cadastros/usuarios/{user}',              [UserController::class, 'update']    )->name('usuarios.update');
    Route::post('/cadastros/usuarios/{user}/toggle',      [UserController::class, 'toggleAtivo'])->name('usuarios.toggle');
});
