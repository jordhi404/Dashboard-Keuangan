<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated as MiddlewareRedirectIfAuthenticated;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Debugbar', \Barryvdh\Debugbar\Facades\Debugbar::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('no-cache', RedirectIfAuthenticated::class);

        Paginator::useBootstrap();

        Blade::directive('sortableCustom', function ($expression) {
            list($route, $column) = explode(',', str_replace(['(', ')', ' '], '', $expression));
            return "<?php echo route($route, array_merge(request()->query(), ['order_by' => {$column}, 'sort_by' => request('sort_by') === 'asc' ? 'desc' : 'asc'])); ?>";
        });
    }
}
