<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index()
    {
        $cart          = $this->cartService->getCartWithItems();
        $subtotal      = $this->cartService->getCartTotal();
        $discount      = $this->cartService->getCouponDiscount($subtotal);
        $shippingCost  = $this->cartService->calculateShipping($subtotal);
        $total         = round($subtotal - $discount + $shippingCost, 2);
        $appliedCoupon = $this->cartService->getAppliedCoupon();

        return view('cart.index', compact('cart', 'subtotal', 'discount', 'shippingCost', 'total', 'appliedCoupon'));
    }

    public function applyCoupon(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $result = $this->cartService->applyCouponCode($request->string('code')->toString());

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return $result['success']
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    public function removeCoupon(\Illuminate\Http\Request $request)
    {
        $this->cartService->removeCoupon();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Coupon removed.']);
        }

        return back()->with('success', 'Coupon removed.');
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        $result = $this->cartService->addItem(
            $request->integer('product_id'),
            $request->integer('quantity', 1)
        );

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return $result['success']
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    public function update(Request $request, int $cartItemId)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $result = $this->cartService->updateItem($cartItemId, $request->integer('quantity'));

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return $result['success']
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    public function remove(int $cartItemId)
    {
        $result = $this->cartService->removeItem($cartItemId);

        if (request()->expectsJson()) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }
}
