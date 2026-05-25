<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    protected $table    = 'vendedores';
    protected $fillable = ['nome', 'setor', 'telefone', 'email', 'ativo'];
    protected $casts    = ['ativo' => 'boolean'];

    public function pedidos() { return $this->hasMany(Pedido::class); }

    public function scopeAtivo($query) { return $query->where('ativo', true); }
}
