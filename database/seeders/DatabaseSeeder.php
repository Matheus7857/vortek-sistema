<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use App\Models\Rota;
use App\Models\User;
use App\Models\Vendedor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuários
        $admin = User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@frios.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);
        User::create([
            'name'        => 'Ana Paula',
            'email'       => 'ana@frios.com',
            'password'    => Hash::make('frios123'),
            'role'        => 'faturamento',
            'permissions' => ['dashboard', 'pedidos', 'relatorio', 'calendario', 'cadastros'],
        ]);
        $producao = User::create([
            'name'        => 'Pedro',
            'email'       => 'pedro@frios.com',
            'password'    => Hash::make('frios123'),
            'role'        => 'producao',
            'permissions' => ['painel', 'kds', 'conferencia'],
        ]);

        // Rotas
        $r1 = Rota::create(['codigo' => 'R1', 'nome' => 'R1 — Centro',   'motorista' => 'João',   'dias_atendimento' => 'Seg, Qua, Sex', 'regiao' => 'Centro, Sé, República']);
        $r2 = Rota::create(['codigo' => 'R2', 'nome' => 'R2 — Norte',    'motorista' => 'Marcos',  'dias_atendimento' => 'Ter, Qui',      'regiao' => 'Santana, Tucuruvi']);
        $r3 = Rota::create(['codigo' => 'R3', 'nome' => 'R3 — Sul',      'motorista' => 'Roberto', 'dias_atendimento' => 'Seg, Qui',      'regiao' => 'Santo André, Diadema']);
        $r4 = Rota::create(['codigo' => 'R4', 'nome' => 'R4 — Leste',    'motorista' => 'Fábio',   'dias_atendimento' => 'Qua, Sex',      'regiao' => 'Mogi, Guararema']);
        Rota::create(['codigo' => 'R5', 'nome' => 'R5 — Oeste', 'motorista' => null, 'dias_atendimento' => null, 'ativo' => false]);

        // Clientes
        $c1 = Cliente::create(['nome' => 'Mercado São João',    'rota_id' => $r1->id, 'cidade' => 'São Paulo',    'telefone' => '(11) 3333-1001']);
        $c2 = Cliente::create(['nome' => 'Açougue Central',     'rota_id' => $r2->id, 'cidade' => 'Guarulhos',    'telefone' => '(11) 3333-1002']);
        $c3 = Cliente::create(['nome' => 'Padaria Boa Vista',   'rota_id' => $r1->id, 'cidade' => 'São Paulo',    'telefone' => '(11) 3333-1003']);
        $c4 = Cliente::create(['nome' => 'Distribuidora Norte', 'rota_id' => $r3->id, 'cidade' => 'Osasco',       'telefone' => '(11) 3333-1004']);
        Cliente::create(['nome' => 'Supermercado Flash', 'rota_id' => $r4->id, 'cidade' => 'Mogi das Cruzes', 'ativo' => false]);

        // Vendedores
        $v1 = Vendedor::create(['nome' => 'Ana Paula',    'setor' => 'faturamento']);
        $v2 = Vendedor::create(['nome' => 'Carlos Souza', 'setor' => 'vendas']);
        Vendedor::create(['nome' => 'Pedro', 'setor' => 'producao']);

        // Produtos
        $produtos = [
            ['nome' => 'Mortadela',      'categoria' => 'embutido', 'tipo_padrao' => 'fracionado', 'unidade' => 'kg'],
            ['nome' => 'Presunto',       'categoria' => 'embutido', 'tipo_padrao' => 'fracionado', 'unidade' => 'kg'],
            ['nome' => 'Salame',         'categoria' => 'embutido', 'tipo_padrao' => 'fracionado', 'unidade' => 'kg'],
            ['nome' => 'Linguiça',       'categoria' => 'embutido', 'tipo_padrao' => 'kilo',       'unidade' => 'kg'],
            ['nome' => 'Copa',           'categoria' => 'embutido', 'tipo_padrao' => 'fracionado', 'unidade' => 'kg'],
            ['nome' => 'Peito de Peru',  'categoria' => 'embutido', 'tipo_padrao' => 'fracionado', 'unidade' => 'kg'],
            ['nome' => 'Apresuntado',    'categoria' => 'embutido', 'tipo_padrao' => 'fracionado', 'unidade' => 'kg'],
            ['nome' => 'Paio',           'categoria' => 'embutido', 'tipo_padrao' => 'kilo',       'unidade' => 'kg'],
            ['nome' => 'Calabresa',      'categoria' => 'embutido', 'tipo_padrao' => 'kilo',       'unidade' => 'kg'],
            ['nome' => 'Muçarela',       'categoria' => 'queijo',   'tipo_padrao' => 'fracionado', 'unidade' => 'kg'],
            ['nome' => 'Queijo Prato',   'categoria' => 'queijo',   'tipo_padrao' => 'fracionado', 'unidade' => 'kg'],
            ['nome' => 'Provolone',      'categoria' => 'queijo',   'tipo_padrao' => 'fracionado', 'unidade' => 'kg'],
            ['nome' => 'Cheddar',        'categoria' => 'queijo',   'tipo_padrao' => 'fracionado', 'unidade' => 'kg'],
            ['nome' => 'Parmesão',       'categoria' => 'queijo',   'tipo_padrao' => 'fracionado', 'unidade' => 'kg'],
            ['nome' => 'Bacon',          'categoria' => 'outro',    'tipo_padrao' => 'kilo',       'unidade' => 'kg'],
        ];

        $prods = [];
        foreach ($produtos as $p) {
            $prods[] = Produto::create($p);
        }

        // Pedidos de exemplo
        $hoje  = Carbon::today();
        $dados_pedidos = [
            ['nome' => 'Mercado São João',    'vendedor' => $v1, 'rota' => $r1, 'dias' => 0,  'prio' => 'urgente', 'status' => 'enviado',
             'itens' => [[$prods[0], 'fracionado', 2.5, 'kg'], [$prods[9], 'fracionado', 1.8, 'kg']]],
            ['nome' => 'Açougue Central',     'vendedor' => $v2, 'rota' => $r2, 'dias' => 0,  'prio' => 'alta',    'status' => 'producao',
             'itens' => [[$prods[1], 'fracionado', 3.0, 'kg'], [$prods[4], 'fracionado', 1.5, 'kg']]],
            ['nome' => 'Padaria Boa Vista',   'vendedor' => $v1, 'rota' => $r1, 'dias' => 2,  'prio' => 'normal',  'status' => 'enviado',
             'itens' => [[$prods[2], 'fracionado', 1.2, 'kg'], [$prods[10], 'fracionado', 0.8, 'kg']]],
            ['nome' => 'Distribuidora Norte', 'vendedor' => $v2, 'rota' => $r3, 'dias' => 5,  'prio' => 'normal',  'status' => 'enviado',
             'itens' => [[$prods[8], 'kilo', 5.0, 'kg'], [$prods[3], 'kilo', 3.0, 'kg']]],
            ['nome' => 'Mercado São João',    'vendedor' => $v1, 'rota' => $r1, 'dias' => -1, 'prio' => 'urgente', 'status' => 'producao',
             'itens' => [[$prods[5], 'fracionado', 2.0, 'kg'], [$prods[11], 'fracionado', 1.0, 'kg']]],
        ];

        foreach ($dados_pedidos as $d) {
            $pedido = Pedido::create([
                'cliente_nome' => $d['nome'],
                'vendedor_id'  => $d['vendedor']->id,
                'rota_id'      => $d['rota']->id,
                'data_saida'   => $hoje->copy()->addDays($d['dias']),
                'prioridade'   => $d['prio'],
                'status'       => $d['status'],
                'criado_por'   => $admin->id,
            ]);
            foreach ($d['itens'] as [$prod, $tipo, $qtd, $unid]) {
                PedidoItem::create([
                    'pedido_id'  => $pedido->id,
                    'produto_id' => $prod->id,
                    'tipo'       => $tipo,
                    'quantidade' => $qtd,
                    'unidade'    => $unid,
                ]);
            }
        }
    }
}
