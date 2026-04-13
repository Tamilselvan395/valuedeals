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
            btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add';
        }
    });
};

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

function updateCartBadge(count) {
    const badge = document.getElementById('cart-count');
    if (badge) badge.textContent = count;
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-5 py-3 rounded-xl shadow-lg text-white text-sm font-medium flex items-center gap-2 transition-all duration-300 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
    toast.innerHTML = (type === 'success' ? '✓ ' : '✕ ') + message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}
