<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nome', 'contato', 'telefone', 'email', 'cpf_cnpj',
        'endereco', 'cidade', 'rota_id', 'observacoes', 'ativo',
    ];
    protected $casts = ['ativo' => 'boolean'];

    public function rota()    { return $this->belongsTo(Rota::class); }
    public function pedidos() { return $this->hasMany(Pedido::class); }

    public function scopeAtivo($query) { return $query->where('ativo', true); }
}
