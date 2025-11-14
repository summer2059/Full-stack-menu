<aside class="cart position-fixed top-0 end-0 m-3 p-3 bg-light rounded shadow" id="cart">
    <h2>
        🛒
        <button class="close-cart" onclick="closeCart()">✖</button>
    </h2>
    <div class="cart-items" id="cart-items"></div>
    <div class="cart-summary">
        Total: NRs.<span id="cart-total">0.00</span>
        <button class="checkout" onclick="openCheckout()">Checkout</button>
    </div>
</aside>
<script>
    function closeCart() {
        document.getElementById('cart').classList.remove('active');
    }
</script>