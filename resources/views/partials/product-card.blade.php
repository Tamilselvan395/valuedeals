<style>
/* ── Product card responsive tweaks ─────────────────── */
.pc-add-btn {
    background: #feee00;
    color: #000;
    border: none;
    cursor: pointer;
    font-weight: 900;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    transition: background 0.15s, transform 0.1s;
    box-shadow: 0 2px 8px rgba(254,238,0,0.45);
    white-space: nowrap;
    /* desktop default */
    font-size: 12px;
    padding: 8px 14px;
}
.pc-add-btn:hover { background: #f5e500; transform: scale(1.05); }
.pc-add-btn:active { transform: scale(0.95); }

.pc-stepper {
    display: none;
    align-items: center;
    background: #feee00;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(254,238,0,0.45);
}
.pc-stepper-btn {
    border: none;
    background: transparent;
    cursor: pointer;
    font-weight: 900;
    color: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.1s;
    /* desktop default */
    width: 30px;
    height: 30px;
    font-size: 18px;
}
.pc-stepper-btn:hover { background: rgba(0,0,0,0.08); }
.pc-stepper-qty {
    font-weight: 900;
    color: #000;
    text-align: center;
    font-size: 13px;
    min-width: 22px;
}

/* ── Mobile only: stack Add button below price ───────── */
@media (max-width: 639px) {
    /* Make price+button row vertical */
    .pc-price-row {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 6px !important;
        min-height: unset !important;
    }
    /* Ctrl wrapper fills full card width */
    .pc-ctrl-wrap {
        width: 100%;
    }
    /* Add button: full-width, rounded pill */
    .pc-add-btn {
        width: 100%;
        font-size: 11px;
        font-weight: 800;
        padding: 7px 6px;
        gap: 4px;
        border-radius: 10px;
        box-shadow: 0 1px 6px rgba(254,238,0,0.5);
    }
    /* Always show "Add" label text on mobile */
    .pc-add-btn .pc-add-label { display: inline; }
    /* Stepper: full-width pill with space between − qty + */
    .pc-stepper {
        width: 100%;
        border-radius: 10px;
        justify-content: space-between;
    }
    .pc-stepper-btn {
        width: 32px;
        height: 30px;
        font-size: 18px;
    }
    .pc-stepper-qty {
        font-size: 13px;
        min-width: 24px;
    }
}
</style>

<div class="group bg-white rounded-2xl border border-gray-100 hover:border-yellow-200 hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden h-full relative">

    {{-- Image --}}
    <a href="{{ route('shop.show', $product->slug) }}" class="block relative bg-gray-50 overflow-hidden flex-shrink-0" style="height:190px;">

        @if($product->discount_percentage > 0)
        <span style="position:absolute;top:8px;left:8px;z-index:10;background:#16a34a;color:#fff;font-size:9px;font-weight:900;padding:3px 7px;border-radius:20px;letter-spacing:0.03em;">
            {{ $product->discount_percentage }}% OFF
        </span>
        @endif

        @if($product->stock == 0)
        <div style="position:absolute;inset:0;background:rgba(255,255,255,0.72);z-index:10;display:flex;align-items:center;justify-content:center;">
            <span style="background:#111;color:#fff;font-size:9px;font-weight:900;padding:4px 12px;border-radius:20px;letter-spacing:0.05em;">SOLD OUT</span>
        </div>
        @endif

        @if($product->cover_image)
        <img src="{{ Storage::url($product->cover_image) }}" alt="{{ $product->title }}"
             class="w-full h-full object-contain p-3 group-hover:scale-105 transition-transform duration-500">
        @else
        <div class="w-full h-full flex items-center justify-center" style="font-size:48px;">📖</div>
        @endif
    </a>

    {{-- Details --}}
    <div class="p-2 sm:p-3 flex flex-col flex-1">

        @if($product->category)
        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#9ca3af;margin-bottom:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $product->category->name }}</p>
        @endif

        <a href="{{ route('shop.show', $product->slug) }}">
            <h3 style="font-size:14px;font-weight:700;color:#111827;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:4px;">{{ $product->title }}</h3>
        </a>

        <!-- @if($product->author)
        <p style="font-size:10px;color:#9ca3af;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $product->author }}</p>
        @endif -->

        {{-- Price + Control --}}
        <div style="margin-top:auto;padding-top:8px;border-top:1px solid #f3f4f6;">
            <div class="pc-price-row" style="display:flex;align-items:center;justify-content:space-between;gap:4px;">

                {{-- Price --}}
                <div style="min-width:0;flex:1;">
                    <p style="font-size:17px;font-weight:700;color:#111;line-height:1;white-space:nowrap;letter-spacing:-0.3px;">
                        {{ config('bookstore.currency_symbol') }}{{ number_format($product->selling_price, 0) }}
                    </p>
                    @if($product->discount_price)
                    <p style="font-size:12px;color:#9ca3af;text-decoration:line-through;line-height:1;margin-top:3px;white-space:nowrap;">
                        {{ config('bookstore.currency_symbol') }}{{ number_format($product->price, 0) }}
                    </p>
                    @endif
                </div>

                @if($product->stock > 0)
                <div id="card-ctrl-{{ $product->id }}" class="pc-ctrl-wrap">

                    {{-- ADD button --}}
                    <button id="card-add-{{ $product->id }}"
                        class="pc-add-btn"
                        onclick="cardAddToCart({{ $product->id }}, this)">
                        <svg style="width:11px;height:11px;flex-shrink:0;" fill="none" stroke="#000" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span class="pc-add-label">Add</span>
                    </button>

                    {{-- QTY STEPPER --}}
                    <div id="card-stepper-{{ $product->id }}" class="pc-stepper">
                        <button class="pc-stepper-btn" onclick="cardDecrement({{ $product->id }})" aria-label="Decrease">−</button>
                        <span id="card-qty-{{ $product->id }}" class="pc-stepper-qty">1</span>
                        <button class="pc-stepper-btn" onclick="cardIncrement({{ $product->id }})" aria-label="Increase">+</button>
                    </div>

                </div>
                @else
                {{-- SOLD OUT button — visible but disabled --}}
                <div class="pc-ctrl-wrap">
                    <button disabled
                        style="background:#e5e7eb;color:#9ca3af;border:none;cursor:not-allowed;font-weight:800;border-radius:20px;display:flex;align-items:center;justify-content:center;gap:4px;font-size:11px;padding:8px 12px;white-space:nowrap;width:100%;">
                        <svg style="width:11px;height:11px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Sold Out
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
