<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue  = Order::where('status', '!=', 'cancelled')->sum('total');
        $monthRevenue  = Order::where('status', '!=', 'cancelled')
                              ->whereMonth('created_at', now()->month)->sum('total');
        $lastMonthRev  = Order::where('status', '!=', 'cancelled')
                              ->whereMonth('created_at', now()->subMonth()->month)->sum('total');
        $revenueChange = $lastMonthRev > 0
                         ? round((($monthRevenue - $lastMonthRev) / $lastMonthRev) * 100, 1)
                         : 0;

        $totalOrders   = Order::count();
        $todayOrders   = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        $totalProducts = Product::count();
        $lowStock      = Product::where('stock', '<', 5)->where('stock', '>', 0)->count();
        $outOfStock    = Product::where('stock', 0)->count();

        $totalUsers    = User::where('role', 'customer')->count();
        $newUsers      = User::where('role', 'customer')
                            ->whereMonth('created_at', now()->month)->count();

        return [
            Stat::make('Total Revenue', config('bookstore.currency_symbol') . number_format($totalRevenue, 2))
                ->description($revenueChange >= 0 ? "+{$revenueChange}% from last month" : "{$revenueChange}% from last month")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart(
                    Order::where('status', '!=', 'cancelled')
                         ->whereDate('created_at', '>=', now()->subDays(7))
                         ->selectRaw('DATE(created_at) as date, SUM(total) as total')
                         ->groupBy('date')->orderBy('date')
                         ->pluck('total')->toArray()
                ),

            Stat::make('Total Orders', number_format($totalOrders))
                ->description("{$todayOrders} today · {$pendingOrders} pending")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

            Stat::make('Products', number_format($totalProducts))
                ->description("{$lowStock} low stock · {$outOfStock} out of stock")
                ->descriptionIcon('heroicon-m-book-open')
                ->color($outOfStock > 0 ? 'danger' : 'info'),

            Stat::make('Customers', number_format($totalUsers))
                ->description("+{$newUsers} this month")
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}
