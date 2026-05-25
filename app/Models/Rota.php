<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rota extends Model
{
    protected $fillable = ['codigo', 'nome', 'motorista', 'telefone_motorista', 'regiao', 'dias_atendimento', 'ativo'];
    protected $casts    = ['ativo' => 'boolean'];

    public function clientes() { return $this->hasMany(Cliente::class); }
    public function pedidos()  { return $this->hasMany(Pedido::class); }

    public function scopeAtivo($query) { return $query->where('ativo', true); }
}
