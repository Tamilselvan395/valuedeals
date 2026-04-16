window.addToCart = function(productId, buttonEl = null, quantity = 1) {
    const btn = buttonEl || event?.currentTarget;
    if (btn) { btn.disabled = true; btn.innerHTML = '...'; }

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: productId, quantity: parseInt(quantity) }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            updateCartBadge(data.cart_count);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(() => showToast('Something went wrong.', 'error'))
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<svg style="width:11px;height:11px;" fill="none" stroke="#000" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Add';
        }
    });
};

// ── Card inline stepper ──────────────────────────────────────────────────────

// Tracks qty per product on the card: { productId: { cartItemId, qty } }
const _cardState = {};

window.cardAddToCart = function(productId, btnEl) {
    btnEl.disabled = true;
    btnEl.textContent = '…';

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Store cart item ID returned from server so we can update/remove later
            _cardState[productId] = { cartItemId: data.cart_item_id, qty: 1 };
            _showStepper(productId, 1);
            updateCartBadge(data.cart_count);
        } else {
            btnEl.disabled = false;
            btnEl.innerHTML = '<svg style="width:11px;height:11px;" fill="none" stroke="#000" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Add';
            showToast(data.message, 'error');
        }
    })
    .catch(() => {
        btnEl.disabled = false;
        btnEl.innerHTML = 'Add';
        showToast('Something went wrong.', 'error');
    });
};

window.cardIncrement = function(productId) {
    const state = _cardState[productId];
    if (!state) return;
    const newQty = state.qty + 1;
    _patchCardQty(productId, newQty);
};

window.cardDecrement = function(productId) {
    const state = _cardState[productId];
    if (!state) return;
    const newQty = state.qty - 1;
    if (newQty <= 0) {
        // Remove from cart and show Add button again
        fetch(`/cart/remove/${state.cartItemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            delete _cardState[productId];
            _showAddBtn(productId);
            updateCartBadge(data.cart_count);
        });
    } else {
        _patchCardQty(productId, newQty);
    }
};

function _patchCardQty(productId, qty) {
    const state = _cardState[productId];
    fetch(`/cart/update/${state.cartItemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ quantity: qty }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            state.qty = qty;
            const qtyEl = document.getElementById(`card-qty-${productId}`);
            if (qtyEl) qtyEl.textContent = qty;
            updateCartBadge(data.cart_count);
        }
    });
}

function _showStepper(productId, qty) {
    const addBtn = document.getElementById(`card-add-${productId}`);
    const stepper = document.getElementById(`card-stepper-${productId}`);
    const qtyEl = document.getElementById(`card-qty-${productId}`);
    if (addBtn) addBtn.style.display = 'none';
    if (stepper) stepper.style.display = 'flex';
    if (qtyEl) qtyEl.textContent = qty;
}

function _showAddBtn(productId) {
    const addBtn = document.getElementById(`card-add-${productId}`);
    const stepper = document.getElementById(`card-stepper-${productId}`);
    if (addBtn) { addBtn.style.display = 'flex'; addBtn.disabled = false; }
    if (stepper) stepper.style.display = 'none';
}

// ── Cart page functions ──────────────────────────────────────────────────────

window.updateCartItem = function(cartItemId, quantity) {
    fetch(`/cart/update/${cartItemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ quantity }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (quantity <= 0) {
                document.getElementById(`cart-item-${cartItemId}`)?.remove();
            } else {
                const qtyEl = document.getElementById(`qty-${cartItemId}`);
                const subEl = document.getElementById(`subtotal-${cartItemId}`);
                if (qtyEl) qtyEl.textContent = quantity;
                if (subEl && data.item_subtotal) {
                    const sym = document.querySelector('meta[name="currency-symbol"]')?.content || '';
                    subEl.textContent = sym + data.item_subtotal;
                }
            }
            updateCartBadge(data.cart_count);
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    });
};

window.removeCartItem = function(cartItemId) {
    fetch(`/cart/remove/${cartItemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById(`cart-item-${cartItemId}`)?.remove();
        updateCartBadge(data.cart_count);
        showToast(data.message, 'success');
    });
};

// ── Helpers ──────────────────────────────────────────────────────────────────

function updateCartBadge(count) {
    ['cart-count', 'mobile-cart-badge'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = count;
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.style.cssText = `position:fixed;top:20px;right:20px;z-index:9999;padding:12px 20px;border-radius:12px;color:#fff;font-size:13px;font-weight:700;display:flex;align-items:center;gap:8px;box-shadow:0 8px 24px rgba(0,0,0,0.15);background:${type === 'success' ? '#16a34a' : '#dc2626'};transition:opacity 0.3s;`;
    toast.innerHTML = (type === 'success' ? '✓ ' : '✕ ') + message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}
