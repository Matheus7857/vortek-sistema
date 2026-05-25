<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $fillable = ['nome', 'categoria', 'tipo_padrao', 'unidade', 'peso_minimo', 'ativo', 'observacoes'];
    protected $casts    = ['ativo' => 'boolean', 'peso_minimo' => 'decimal:3'];

    public function pedidoItens() { return $this->hasMany(PedidoItem::class); }

    public function scopeAtivo($query) { return $query->where('ativo', true); }
}
