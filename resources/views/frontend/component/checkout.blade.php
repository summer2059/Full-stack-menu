<div class="checkout-form" id="checkout-form" style="display:none; padding: 20px; background: #fff; border: 1px solid #ccc; max-width: 500px; margin: 20px auto;">
  <h2>
    Checkout 
    <button class="close-checkout" onclick="closeCheckout()" style="float:right; background:none; border:none; font-size:18px; cursor:pointer;">✖</button>
  </h2>
  <form id="order-form" method="POST" action="{{ route('order.submit') }}">
    @csrf

    <label>Full Name:</label>
    <input type="text" id="name" name="name" required style="width: 100%; margin-bottom: 10px; padding: 5px;">

    <label>Table Number:</label>
    <input type="number" id="table" name="table" required min="1" style="width: 100%; margin-bottom: 10px; padding: 5px;">

    <label>Phone (optional):</label>
    <input type="text" id="phone" name="phone" style="width: 100%; margin-bottom: 10px; padding: 5px;">

    <label>Special Instructions:</label>
    <textarea id="notes" name="notes" rows="3" style="width: 100%; margin-bottom: 20px; padding: 5px;"></textarea>

    <h3>Your Order:</h3>
    <div id="checkout-items" style="margin-bottom: 15px;"></div>

    <p><strong>Total: $<span id="checkout-total">0.00</span></strong></p>

    <button type="submit" style="padding: 10px 20px; margin-right: 10px; cursor:pointer;">Confirm Order</button>
    <button type="button" onclick="closeCheckout()" style="padding: 10px 20px; cursor:pointer;">Cancel</button>
  </form>
</div>
