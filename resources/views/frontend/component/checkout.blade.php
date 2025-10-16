<div class="checkout-form" id="checkout-form" style="display:none;">
  <h2>
    Checkout 
    <button class="close-checkout" onclick="closeCheckout()" style="float:right;">✖</button>
  </h2>
  <form id="order-form" method="POST" action="{{ route('order.submit') }}">
    @csrf

    <label>Full Name:</label>
    <input type="text" id="name" name="name" required>

    <label>Table Number:</label>
    <input type="number" id="table" name="table" required min="1">

    <label>Phone (optional):</label>
    <input type="text" id="phone" name="phone">

    <label>Special Instructions:</label>
    <textarea id="notes" name="notes" rows="3"></textarea>

    <h3>Your Order:</h3>
    <div id="checkout-items" style="margin-bottom: 15px;"></div>

    <p><strong>Total: NRs.<span id="checkout-total">0.00</span></strong></p>

    <button type="submit">Confirm Order</button>
    <button type="button" onclick="closeCheckout()">Cancel</button>
  </form>
</div>
