<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('produto_id')->constrained('produtos');
            $table->enum('tipo', ['fracionado', 'kilo'])->default('fracionado');
            $table->decimal('quantidade', 10, 3);
            $table->enum('unidade', ['kg', 'g', 'un', 'pct'])->default('kg');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_itens');
    }
};
