{{-- ── Checkout Modal ── --}}
<div class="checkout-form" id="checkout-form" role="dialog" aria-modal="true" aria-labelledby="checkout-title">
    <div class="checkout-content">
        <h5 id="checkout-title">
            Place Order
            <button class="close-checkout" onclick="closeCheckout()" aria-label="Close">✕</button>
        </h5>
        <form id="order-form" method="POST" action="{{ route('order.submit') }}">
            @csrf

            {{-- Hidden customer UUID --}}
            <input type="hidden" name="uuid" id="checkout-uuid">

            <div class="form-group">
                <label for="co-name">Full Name</label>
                <input type="text" id="co-name" name="user_name" placeholder="Your name" required>
            </div>

            <div class="form-group">
                <label>
                    Table Number
                    @if(!empty($tableNumber))
                        <span class="table-verified-badge">
                            <i class="fas fa-qrcode"></i> Scanned
                        </span>
                    @endif
                </label>
                @if(!empty($tableNumber))
                    <div class="table-readonly-field">
                        <i class="fas fa-chair"></i> Table {{ $tableNumber }}
                    </div>
                    <input type="hidden" name="table" value="{{ $tableNumber }}">
                @else
                    <input type="number" name="table" placeholder="e.g. 5" required min="1" value="{{ old('table') }}">
                @endif
            </div>

            <div class="form-group">
                <label for="co-phone">Phone <span style="opacity:.5;font-weight:400">(optional)</span></label>
                <input type="tel" id="co-phone" name="user_phone" placeholder="+977 98XXXXXXXX">
            </div>

            {{-- Order Summary with per-item note inputs rendered by JS --}}
            <div class="order-summary-box">
                <h6>
                    Your Order
                    <small style="font-size:0.75rem;color:var(--muted);font-family:var(--font-body)">(add a note per item)</small>
                </h6>
                <div id="checkout-items"></div>
                <div class="checkout-total-row">
                    <span>Total</span>
                    <span>NRs.<span id="checkout-total-display">0.00</span></span>
                </div>
                <span id="checkout-total" style="display:none">0.00</span>
            </div>

            <div class="form-group" style="margin-top:0.25rem">
                <label for="co-note">General Note <span style="opacity:.5;font-weight:400">(optional)</span></label>
                <textarea id="co-note" name="note" rows="2" placeholder="Allergies, preferences…"></textarea>
            </div>

            <button type="submit" class="btn-submit">Confirm Order ✓</button>
            <button type="button" class="btn-cancel" onclick="closeCheckout()">Cancel</button>
        </form>
    </div>
</div>