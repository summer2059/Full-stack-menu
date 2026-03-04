{{-- ── Track Order Modal ── --}}
<div class="track-order-modal" id="track-order-modal" role="dialog" aria-modal="true" aria-labelledby="track-modal-title">
    <div class="track-modal-content">
        <div class="track-modal-header">
            <h5 id="track-modal-title">📋 Track My Order</h5>
            <button class="close-track" onclick="closeTrackOrder()" aria-label="Close">✕</button>
        </div>
        <div id="track-order-list">
            <div class="track-empty">
                <span>📋</span>
                <p>Loading your orders…</p>
            </div>
        </div>
    </div>
</div>