<?php

namespace App\Providers;

use App\Listeners\MergeCartOnLogin;
use App\Models\Order;
use App\Models\StoreSetting;
use App\Policies\OrderPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        StoreSetting::loadIntoConfig();

        Gate::policy(Order::class, OrderPolicy::class);

        Event::listen(Login::class, MergeCartOnLogin::class);
    }
}
