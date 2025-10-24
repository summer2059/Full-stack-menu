<aside class="cart position-fixed top-0 end-0 m-3 p-3 bg-light rounded shadow" id="cart">
    <h5 class="d-flex justify-content-between align-items-center">
        🛒 Cart 
        <button class="close-cart btn btn-sm btn-outline-secondary" onclick="closeCart()">✖</button>
    </h5>
    <div id="cart-items" class="cart-items mb-3"></div>
    <p class="fw-bold">Total: NRs.<span id="cart-total">0.00</span></p>
    <button class="checkout btn btn-success w-100" onclick="openCheckout()">Checkout</button>
</aside>
