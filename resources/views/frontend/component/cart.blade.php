{{-- ── Cart Sidebar ── --}}
<aside class="cart" id="cart" aria-label="Shopping Cart">
    <div class="cart-header">
        <h2>🛒 Your Cart</h2>
        <button class="close-cart" onclick="closeCart()" aria-label="Close cart">✕</button>
    </div>
    <div class="cart-items" id="cart-items">
        <div class="cart-empty">
            <span class="empty-icon">🍽️</span>
            <p>Your cart is empty</p>
        </div>
    </div>
    <div class="cart-footer">
        <div class="cart-total-row">
            <span class="cart-total-label">Selected Total</span>
            <span class="cart-total-amount">NRs.<span id="cart-total">0.00</span></span>
        </div>
        <button class="checkout" onclick="openCheckout()">
            Proceed to Checkout →
        </button>
    </div>
</aside>