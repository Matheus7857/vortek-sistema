<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('numero')->unique();
            $table->string('cliente_nome', 255)->nullable();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('vendedor_id')->constrained('vendedores');
            $table->foreignId('rota_id')->nullable()->constrained('rotas')->nullOnDelete();
            $table->date('data_saida');
            $table->enum('prioridade', ['normal', 'alta', 'urgente'])->default('normal');
            $table->text('observacoes')->nullable();
            $table->enum('status', ['rascunho', 'enviado', 'confirmado', 'producao', 'conferido', 'pronto'])
                ->default('rascunho');
            $table->foreignId('criado_por')->constrained('users');
            $table->foreignId('confirmado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmado_em')->nullable();
            $table->foreignId('conferido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('conferido_em')->nullable();
            $table->text('observacoes_conferencia')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};