<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function () {
            if (request()->routeIs('checkout.index', 'checkout.store', 'checkout.stripe.success', 'checkout.stripe.cancel')) {
                return route('register', ['checkout' => '1']);
            }

            return route('login');
        });
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('bookstore:store-abandoned-carts')->hourly();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
