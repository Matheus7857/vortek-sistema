<?php

namespace App\Providers;

use App\Models\Pedido;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Directive @active para marcar link atual no sidebar
        Blade::directive('active', function ($route) {
            return "<?php echo request()->routeIs({$route}) ? 'active' : ''; ?>";
        });

        // Compartilha contagem de pedidos pendentes de recepção para o badge do sidebar
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $view->with('pendentesRecepcao', Pedido::where('status', 'enviado')->count());
            }
        });
    }
}
