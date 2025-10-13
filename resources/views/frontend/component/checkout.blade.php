<div class="checkout-form" id="checkout-form">
    <h2>Checkout <button class="close-checkout" onclick="closeCheckout()">✖</button></h2>
    <form id="order-form">
        <label>Full Name:</label>
        <input type="text" id="name" required>

        <label>Table Number:</label>
        <input type="number" id="table" required min="1">

        <label>Phone (optional):</label>
        <input type="text" id="phone">

        <label>Special Instructions:</label>
        <textarea id="notes" rows="3"></textarea>

        <button type="submit">Confirm Order</button>
        <button type="button" onclick="closeCheckout()">Cancel</button>
    </form>
</div>
