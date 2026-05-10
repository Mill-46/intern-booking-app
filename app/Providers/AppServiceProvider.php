<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Normalize asset URL when environment contains unevaluated placeholders
        $assetUrl = config('app.asset_url');
        if (is_string($assetUrl) && str_contains($assetUrl, '${')) {
            // If ASSET_URL was set to a literal like "${APP_URL}" (not expanded by host),
            // fall back to APP_URL or use relative assets by clearing the asset_url.
            $appUrl = env('APP_URL') ?: null;
            config(['app.asset_url' => $appUrl]);
        }
    }
}
