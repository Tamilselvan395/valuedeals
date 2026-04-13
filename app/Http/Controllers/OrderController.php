<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\OrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly OrderService   $orderService,
        private readonly InvoiceService $invoiceService
    ) {}

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items'])->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['items.product.images']);

        return view('orders.show', compact('order'));
    }

    public function invoice(Order $order)
    {
        $this->authorize('view', $order);

        return $this->invoiceService->download($order);
    }

    public function cancel(Order $order)
    {
        $this->authorize('view', $order);

        $cancelled = $this->orderService->cancelOrder($order);

        return $cancelled
            ? back()->with('success', 'Order cancelled successfully.')
            : back()->with('error', 'This order cannot be cancelled.');
    }
}
