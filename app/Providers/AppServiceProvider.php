<?php

namespace App\Providers;

use App\Models\TransaksiKeluar;
use App\Models\TransaksiKeluarItem;
use App\Models\TransaksiMasukItem;
use App\Observers\TransaksiKeluarObserver;
use App\Observers\TransaksiKeluarItemObserver;
use App\Observers\TransaksiMasukItemObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        TransaksiKeluar::observe(TransaksiKeluarObserver::class);
        TransaksiKeluarItem::observe(TransaksiKeluarItemObserver::class);
        TransaksiMasukItem::observe(TransaksiMasukItemObserver::class);
        FilamentAsset::register([
            Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/filament.css'),
        ]);
        // Filament::serving(function () {
        //     Filament::registerStyles([
        //         asset('css/filament.css'), // Path ke file CSS Anda
        //     ]);
        // });
    }
}
