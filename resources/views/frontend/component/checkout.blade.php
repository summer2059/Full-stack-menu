<div class="checkout-form modal-dialog-centered modal-dialog-scrollable" id="checkout-form">
  <div class="modal-content p-3 rounded shadow bg-light">
    <h5 class="d-flex justify-content-between align-items-center">
        Checkout
        <button class="close-checkout btn btn-sm btn-outline-secondary" onclick="closeCheckout()">✖</button>
    </h5>
    <form id="order-form" method="POST" action="{{ route('order.submit') }}">
        @csrf
        <div class="mb-2">
            <label class="form-label">Full Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Table Number:</label>
            <input type="number" class="form-control" id="table" name="table" required min="1">
        </div>
        <div class="mb-2">
            <label class="form-label">Phone (optional):</label>
            <input type="text" class="form-control" id="phone" name="phone">
        </div>
        <div class="mb-2">
            <label class="form-label">Special Instructions:</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
        </div>
        <h6>Your Order:</h6>
        <div id="checkout-items" class="mb-2"></div>
        <p class="fw-bold">Total: NRs.<span id="checkout-total">0.00</span></p>
        <button type="submit" class="btn btn-success w-100 mb-2">Confirm Order</button>
        <button type="button" class="btn btn-secondary w-100" onclick="closeCheckout()">Cancel</button>
    </form>
  </div>
</div>
