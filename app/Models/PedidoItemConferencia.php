<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoItemConferencia extends Model
{
    protected $table    = 'pedido_item_conferencias';
    protected $fillable = ['pedido_id', 'pedido_item_id', 'conferido', 'quantidade_conferida', 'observacoes'];
    protected $casts    = ['conferido' => 'boolean', 'quantidade_conferida' => 'decimal:3'];

    public function pedido() { return $this->belongsTo(Pedido::class); }
    public function item()   { return $this->belongsTo(PedidoItem::class, 'pedido_item_id'); }
}
