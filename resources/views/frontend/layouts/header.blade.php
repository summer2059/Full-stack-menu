<header>
    <div class="header-brand">
        <span class="logo-main">🍽 Bistro</span>
        <span class="logo-sub">Fine Dining & Takeaway</span>
    </div>

    <div class="header-actions">

        <button class="track-order-btn" id="track-order-btn" onclick="openTrackOrder()" aria-label="Track Order" style="display:none">
            <i class="fas fa-receipt"></i>
            <span class="track-btn-label">Track Order</span>
        </button>

        <button class="track-order-hamburger" id="track-order-hamburger" onclick="openTrackOrder()" aria-label="Track Order" style="display:none">
            <i class="fas fa-receipt"></i>
        </button>

        <button class="toggle-cart" aria-label="Open cart">
            🛒 Cart
            <span class="cart-badge" style="display:none">0</span>
        </button>

    </div>
</header>