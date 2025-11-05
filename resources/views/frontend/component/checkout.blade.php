<!-- Checkout Modal -->
<div class="checkout-form" id="checkout-form">
  <div class="checkout-content">
    <h5 class="d-flex justify-content-between align-items-center mb-3">
      Checkout
      <button class="close-checkout" onclick="closeCheckout()">✖</button>
    </h5>
    <form id="order-form" method="POST" action="<?= route('order.submit') ?>">
      <?= csrf_field() ?>
      <div class="mb-2">
        <label>Full Name:</label>
        <input type="text" id="name" name="name" required>
      </div>
      <div class="mb-2">
        <label>Table Number:</label>
        <input type="number" id="table" name="table" required min="1">
      </div>
      <div class="mb-2">
        <label>Phone (optional):</label>
        <input type="text" id="phone" name="phone">
      </div>
      <div class="mb-2">
        <label>Special Instructions:</label>
        <textarea id="notes" name="notes" rows="3"></textarea>
      </div>
      <h6>Your Order:</h6>
      <div id="checkout-items" class="mb-2"></div>
      <p class="fw-bold">Total: NRs.<span id="checkout-total">0.00</span></p>
      <button type="submit" class="btn-submit">Confirm Order</button>
      <button type="button" class="btn-cancel" onclick="closeCheckout()">Cancel</button>
    </form>
  </div>
</div>
<script>
  function closeCheckout() {
    document.getElementById('checkout-form').classList.remove('active');
  }
</script>