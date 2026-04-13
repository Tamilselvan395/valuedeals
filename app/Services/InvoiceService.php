<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class InvoiceService
{
    public function download(Order $order): Response
    {
        $order->load(['items.product', 'user']);

        $pdf = Pdf::loadView('orders.invoice', ['order' => $order])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
            ]);

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }
}
