<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'ativo', 'permissions'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['password' => 'hashed', 'ativo' => 'boolean', 'permissions' => 'array'];

    public function pedidosCriados()    { return $this->hasMany(Pedido::class, 'criado_por'); }
    public function pedidosConfirmados(){ return $this->hasMany(Pedido::class, 'confirmado_por'); }
    public function pedidosConferidos() { return $this->hasMany(Pedido::class, 'conferido_por'); }

    public function isAdmin(): bool { return $this->role === 'admin'; }

    public function temPermissao(string $perm): bool
    {
        if ($this->role === 'admin') return true;
        return in_array($perm, $this->permissions ?? []);
    }

    // Mantidos para compatibilidade com código existente
    public function isFaturamento(): bool { return $this->temPermissao('pedidos'); }
    public function isProducao(): bool    { return $this->temPermissao('painel'); }
}
