<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'numero', 'cliente_nome', 'cliente_id', 'vendedor_id', 'rota_id', 'data_saida',
        'prioridade', 'observacoes', 'status', 'criado_por',
        'confirmado_por', 'confirmado_em', 'conferido_por', 'conferido_em',
        'observacoes_conferencia',
    ];

    protected $casts = [
        'data_saida'    => 'date',
        'confirmado_em' => 'datetime',
        'conferido_em'  => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($pedido) {
            if (! $pedido->numero) {
                $pedido->numero = (static::max('numero') ?? 1000) + 1;
            }
        });
    }

    public function getNomeClienteAttribute(): string
    {
        return $this->cliente_nome ?? $this->cliente?->nome ?? '—';
    }

    public function cliente()           { return $this->belongsTo(Cliente::class); }
    public function vendedor()          { return $this->belongsTo(Vendedor::class); }
    public function rota()              { return $this->belongsTo(Rota::class); }
    public function criadoPorUser()     { return $this->belongsTo(User::class, 'criado_por'); }
    public function confirmadoPorUser() { return $this->belongsTo(User::class, 'confirmado_por'); }
    public function conferidoPorUser()  { return $this->belongsTo(User::class, 'conferido_por'); }
    public function itens()             { return $this->hasMany(PedidoItem::class); }
    public function conferencias()      { return $this->hasMany(PedidoItemConferencia::class); }

    public function getIsAtrasadoAttribute(): bool
    {
        return $this->data_saida->lt(Carbon::today())
            && ! in_array($this->status, ['pronto', 'conferido']);
    }

    public function scopeAtivos($query)
    {
        return $query->whereNotIn('status', ['pronto']);
    }

    public static function labelStatus(string $status): string
    {
        return match ($status) {
            'rascunho'  => 'Rascunho',
            'enviado'   => 'Pendente',
            'confirmado'=> 'Confirmado',
            'producao'  => 'Em Produção',
            'conferido' => 'Conferido',
            'pronto'    => 'Pronto',
            default     => $status,
        };
    }

    public static function labelPrioridade(string $p): string
    {
        return match ($p) {
            'normal'  => 'Normal',
            'alta'    => 'Alta',
            'urgente' => 'Urgente',
            default   => $p,
        };
    }
}
