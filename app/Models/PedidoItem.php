<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    protected $table    = 'pedido_itens';
    protected $fillable = ['pedido_id', 'produto_id', 'tipo', 'quantidade', 'unidade'];
    protected $casts    = ['quantidade' => 'decimal:3'];

    public function pedido()      { return $this->belongsTo(Pedido::class); }
    public function produto()     { return $this->belongsTo(Produto::class); }
    public function conferencia() { return $this->hasOne(PedidoItemConferencia::class); }
}
