<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('categoria', ['embutido', 'queijo', 'outro'])->default('embutido');
            $table->enum('tipo_padrao', ['fracionado', 'kilo'])->default('fracionado');
            $table->enum('unidade', ['kg', 'g', 'un', 'pct'])->default('kg');
            $table->decimal('peso_minimo', 8, 3)->nullable();
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};