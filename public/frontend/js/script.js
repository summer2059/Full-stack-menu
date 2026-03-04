document.addEventListener("DOMContentLoaded", () => {

  const UUID_KEY    = 'customer_uuid';
  const UUID_EXPIRY = 'customer_uuid_expiry';
  const TTL_MS      = 24 * 60 * 60 * 1000; // 24 hours for production

  async function getOrCreateUUID() {
    const now    = Date.now();
    const stored = localStorage.getItem(UUID_KEY);
    const expiry = localStorage.getItem(UUID_EXPIRY);

    if (stored && expiry && now < parseInt(expiry)) {
      return stored;
    }

    const data = await apiFetch('/api/customer/uuid', 'POST');
    const uuid = data.uuid;

    localStorage.setItem(UUID_KEY, uuid);
    localStorage.setItem(UUID_EXPIRY, (now + TTL_MS).toString());
    return uuid;
  }

  function scheduleExpiry(delay) {
    setTimeout(() => {
      localStorage.removeItem(UUID_KEY);
      localStorage.removeItem(UUID_EXPIRY);
      customerUUID = null;
    }, delay);
  }

  let cart = []; // [{ cart_id, menu_id, name, price, quantity, total_price, is_select, note }]
  let recommendedMenus = window.MENU_ITEMS || []; // injected from blade

  const cartBox       = document.getElementById("cart");
  const cartItemsEl   = document.getElementById("cart-items");
  const cartTotalEl   = document.getElementById("cart-total");
  const cartBadge     = document.querySelector(".cart-badge");
  const checkoutForm  = document.getElementById("checkout-form");
  const checkoutItems = document.getElementById("checkout-items");
  const backdrop      = document.getElementById("cart-backdrop");

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  async function apiFetch(url, method = 'GET', body = null) {
    const opts = {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
    };
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(url, opts);
    return res.json();
  }

  async function loadCartFromDB() {
    try {
      const data = await apiFetch(`/api/cart?uuid=${encodeURIComponent(customerUUID)}`);
      cart = data.items || [];
      renderCart();
    } catch (e) {
      console.error('Failed to load cart:', e);
    }
  }

  // Header scroll
  const header = document.querySelector("header");
  window.addEventListener("scroll", () => {
    header?.classList.toggle("scrolled", window.scrollY > 10);
  }, { passive: true });

  // Toast 
  function showToast(message) {
    const container = document.querySelector(".toast-container") || (() => {
      const el = document.createElement("div");
      el.className = "toast-container";
      document.body.appendChild(el);
      return el;
    })();
    const toast = document.createElement("div");
    toast.className = "toast";
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 2600);
  }

  // Cart open/close (NO scroll lock changes on qty update)
  function openCart() {
    cartBox.classList.add("active");
    backdrop?.classList.add("active");
    document.body.style.overflow = "hidden";
  }

  function closeCartFn() {
    cartBox.classList.remove("active");
    backdrop?.classList.remove("active");
    document.body.style.overflow = "";
  }

  window.closeCart = closeCartFn;

  document.querySelector(".toggle-cart")?.addEventListener("click", () => {
    cartBox.classList.contains("active") ? closeCartFn() : openCart();
  });

  backdrop?.addEventListener("click", closeCartFn);

  document.addEventListener("click", (e) => {
    if (
      cartBox.classList.contains("active") &&
      !cartBox.contains(e.target) &&
      !e.target.closest(".toggle-cart") &&
      !e.target.closest(".cart-btn") &&
      !e.target.closest(".rec-add-btn")
    ) {
      closeCartFn();
    }
  });

  //Add to Cart 
  window.addToCart = async function (id, name, price) {
    if (!customerUUID) {
      customerUUID = await getOrCreateUUID();
    }

    const existing = cart.find(i => i.menu_id === id);
    if (existing) {
      existing.quantity++;
      existing.total_price = existing.price * existing.quantity;
    } else {
      cart.push({ cart_id: null, menu_id: id, name, price: parseFloat(price), quantity: 1, total_price: parseFloat(price), is_select: 1, note: '' });
    }

    const cartIsOpen = cartBox.classList.contains("active");
    renderCart();

    const btn = document.querySelector(`[data-item-id="${id}"]`);
    if (btn) {
      btn.classList.add("added");
      btn.innerHTML = '✓ Added';
      setTimeout(() => {
        btn.classList.remove("added");
        btn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
      }, 1200);
    }

    // Only open cart if it wasn't already open (i.e. not called from recommended)
    if (!cartIsOpen) openCart();
    showToast(`🛒 ${name} added to cart`);

    try {
      const data = await apiFetch('/api/cart/add', 'POST', {
        uuid: customerUUID,
        menu_id: id,
      });
      if (data.success) {
        await loadCartFromDB();
      }
    } catch (e) {
      console.error('Failed to sync cart:', e);
    }
  };

  // Render Cart 
  function renderCart() {
    cartItemsEl.innerHTML = "";

    if (!cart.length) {
      cartItemsEl.innerHTML = `
        <div class="cart-empty">
          <span class="empty-icon">🍽️</span>
          <p>Your cart is empty</p>
        </div>`;
      cartTotalEl.textContent = "0.00";
      updateBadge(0);
      renderRecommended();
      return;
    }

    let selectedTotal = 0;
    let totalQty = 0;

    cart.forEach((item, idx) => {
      const lineTotal = item.price * item.quantity;
      totalQty += item.quantity;
      if (item.is_select) selectedTotal += lineTotal;

      cartItemsEl.innerHTML += `
        <div class="cart-item${item.is_select ? '' : ' cart-item--unselected'}" id="cart-item-${idx}">
          <label class="cart-item-checkbox" title="${item.is_select ? 'Deselect' : 'Select'} item">
            <input
              type="checkbox"
              class="cart-checkbox"
              ${item.is_select ? 'checked' : ''}
              onchange="toggleSelect(${idx}, this.checked)"
              aria-label="Select ${escHtml(item.name)}"
            >
            <span class="checkmark"></span>
          </label>
          <div class="cart-item-info">
            <div class="cart-item-name">${escHtml(item.name)}</div>
            <div class="cart-item-price">NRs.${(item.price * item.quantity).toFixed(0)}</div>
          </div>
          <div class="cart-actions">
            <button class="qty-btn" onclick="decreaseQty(event, ${idx})" aria-label="Decrease">−</button>
            <span class="qty-display">${item.quantity}</span>
            <button class="qty-btn" onclick="increaseQty(event, ${idx})" aria-label="Increase">+</button>
            <button class="remove-btn" onclick="removeItem(event, ${idx})" aria-label="Remove">✕</button>
          </div>
        </div>`;
    });

    cartTotalEl.textContent = selectedTotal.toFixed(2);
    updateBadge(totalQty);
    renderRecommended();
  }

  // Recommended in Cart 
  function renderRecommended() {
    let recEl = document.getElementById('cart-recommended');
    if (!recEl) {
      recEl = document.createElement('div');
      recEl.id = 'cart-recommended';
      cartItemsEl.appendChild(recEl);
    }

    const cartMenuIds = cart.map(i => i.menu_id);
    const available   = recommendedMenus.filter(m => !cartMenuIds.includes(m.id));
    const picks       = available.sort(() => 0.5 - Math.random()).slice(0, 3);

    if (!picks.length) { recEl.innerHTML = ''; return; }

    recEl.innerHTML = `
      <div class="rec-header">You might also like</div>
      <div class="rec-list">
        ${picks.map(m => `
          <div class="rec-item">
            <img src="${m.image_url}" alt="${escHtml(m.title)}" class="rec-img" loading="lazy">
            <div class="rec-info">
              <div class="rec-name">${escHtml(m.title)}</div>
              <div class="rec-price">NRs.${Number(m.price).toFixed(0)}</div>
            </div>
            <button class="rec-add-btn" onclick="addToCart(${m.id}, '${escHtml(m.title).replace(/'/g,"\\'")}', ${m.price})">+</button>
          </div>
        `).join('')}
      </div>`;
  }

  function updateBadge(count) {
    if (!cartBadge) return;
    cartBadge.textContent = count;
    cartBadge.style.display = count > 0 ? "flex" : "none";
    if (count > 0) {
      cartBadge.classList.add("bump");
      setTimeout(() => cartBadge.classList.remove("bump"), 400);
    }
  }

  // Qty: pass event to stop propagation 
  window.increaseQty = async function (e, idx) {
    e.stopPropagation();
    cart[idx].quantity++;
    cart[idx].total_price = cart[idx].price * cart[idx].quantity;
    updateTotalDisplay();
    updateCartItemEl(idx);
    if (cart[idx].cart_id) {
      await apiFetch('/api/cart/update', 'POST', { cart_id: cart[idx].cart_id, quantity: cart[idx].quantity });
    }
  };

  window.decreaseQty = async function (e, idx) {
    e.stopPropagation();
    if (cart[idx].quantity > 1) {
      cart[idx].quantity--;
      cart[idx].total_price = cart[idx].price * cart[idx].quantity;
      updateTotalDisplay();
      updateCartItemEl(idx);
      if (cart[idx].cart_id) {
        await apiFetch('/api/cart/update', 'POST', { cart_id: cart[idx].cart_id, quantity: cart[idx].quantity });
      }
    } else {
      await removeItem(e, idx);
    }
  };

  // Update just the qty + price in DOM without full re-render (prevents cart closing)
  function updateCartItemEl(idx) {
    const el = document.getElementById(`cart-item-${idx}`);
    if (!el) return;
    el.querySelector('.qty-display').textContent = cart[idx].quantity;
    el.querySelector('.cart-item-price').textContent = `NRs.${(cart[idx].price * cart[idx].quantity).toFixed(0)}`;
  }

  window.removeItem = async function (e, idx) {
    e.stopPropagation();
    const item   = cart[idx];
    const cartId = item.cart_id;
    cart.splice(idx, 1);
    renderCart();
    showToast(`Removed ${item.name}`);
    if (cartId) {
      await apiFetch('/api/cart/remove', 'POST', { cart_id: cartId });
    }
  };

  window.toggleSelect = async function (idx, checked) {
    const item = cart[idx];
    if (!item) return;
    const newVal = checked ? 1 : 0;
    item.is_select = newVal;
    updateTotalDisplay();
    if (item.cart_id) {
      try {
        await apiFetch('/api/cart/toggle-select', 'POST', { cart_id: item.cart_id, is_select: newVal });
      } catch (e) {
        item.is_select = checked ? 0 : 1;
        const checkbox = document.querySelector(`#cart-item-${idx} .cart-checkbox`);
        if (checkbox) checkbox.checked = !checked;
        updateTotalDisplay();
        showToast('Failed to update selection');
      }
    }
  };

  // Per-item note
  window.updateItemNote = function (idx, value) {
    if (cart[idx]) cart[idx].note = value;
  };

  function updateTotalDisplay() {
    let selectedTotal = 0;
    let totalQty = 0;
    cart.forEach(item => {
      totalQty += item.quantity;
      if (item.is_select) selectedTotal += item.price * item.quantity;
    });
    if (cartTotalEl) cartTotalEl.textContent = selectedTotal.toFixed(2);
    updateBadge(totalQty);
    cart.forEach((item, i) => {
      const el = document.getElementById(`cart-item-${i}`);
      if (el) el.classList.toggle('cart-item--unselected', !item.is_select);
    });
  }

  // Checkout
  window.openCheckout = function () {
    const selectedItems = cart.filter(i => i.is_select);
    if (!selectedItems.length) {
      showToast("🛒 Please select at least one item!");
      return;
    }

    const uuidInput = document.getElementById('checkout-uuid');
    if (uuidInput) uuidInput.value = customerUUID;

    checkoutItems.innerHTML = "";
    let total = 0;

    // Render each item row with its own visible note input + hidden fields
    selectedItems.forEach((item, i) => {
      const lineTotal = item.price * item.quantity;
      total += lineTotal;
      checkoutItems.innerHTML += `
        <div class="checkout-item-row">
          <div class="checkout-item-top">
            <span class="checkout-item-name">${escHtml(item.name)} <em class="checkout-item-qty">× ${item.quantity}</em></span>
            <span class="checkout-item-price">NRs.${lineTotal.toFixed(0)}</span>
          </div>
          <input
            type="text"
            class="checkout-item-note"
            name="items[${i}][note]"
            placeholder="Note for ${escHtml(item.name)} (optional)"
            value="${escHtml(item.note || '')}"
            autocomplete="off"
          >
          <input type="hidden" name="items[${i}][menu_id]" value="${item.menu_id}">
        </div>`;
    });

    const totalEl = document.getElementById("checkout-total");
    if (totalEl) totalEl.textContent = total.toFixed(2);
    const displayEl = document.getElementById("checkout-total-display");
    if (displayEl) displayEl.textContent = total.toFixed(2);

    checkoutForm.classList.add("active");
    closeCartFn();
  };

  window.closeCheckout = function () {
    checkoutForm.classList.remove("active");
  };

  checkoutForm?.addEventListener("click", function (e) {
    if (e.target === this) closeCheckout();
  });

  //Category Filter
  const categoryBtns = document.querySelectorAll(".category-btn");
  const menuCards    = document.querySelectorAll(".menu-card");

  categoryBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      const category = btn.dataset.category;
      categoryBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      menuCards.forEach((card, i) => {
        const matches = category === "all" || card.dataset.category === category;
        if (matches) {
          card.style.display = "";
          card.style.animation = "none";
          card.offsetHeight;
          card.style.animation = `card-reveal 0.5s var(--ease-spring) ${(i % 8) * 0.06}s forwards`;
        } else {
          card.style.display = "none";
        }
      });
    });
  });

  window.toggleMobileMenu = function () {
    document.getElementById("category-bar")?.classList.toggle("active");
  };

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeCartFn();
      closeCheckout();
      closeTrackOrder();
    }
  });

  function escHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  //Clear UUID on success, show track btn
  if (document.querySelector('.alert-success, [data-flash="success"]')) {
    // Order was just placed — show track button, clear cart UUID on next load
    showTrackOrderBtn();
    localStorage.removeItem(UUID_KEY);
    localStorage.removeItem(UUID_EXPIRY);
  }

  //Track Order Modal
  window.openTrackOrder = async function () {
    if (!customerUUID) { showToast('No active session found.'); return; }
    const modal = document.getElementById('track-order-modal');
    modal?.classList.add('active');
    document.body.style.overflow = 'hidden';
    await refreshOrderStatus();
  };

  window.closeTrackOrder = function () {
    document.getElementById('track-order-modal')?.classList.remove('active');
    document.body.style.overflow = '';
    clearInterval(trackInterval);
    trackInterval = null;
  };

  let trackInterval = null;

  async function refreshOrderStatus() {
    const container = document.getElementById('track-order-list');
    if (!container) return;
    try {
      const data = await apiFetch(`/api/orders/track?uuid=${encodeURIComponent(customerUUID)}`);
      const orders = data.orders || [];

      if (!orders.length) {
        container.innerHTML = `<div class="track-empty"><span>📋</span><p>No active orders found.</p></div>`;
        return;
      }

      container.innerHTML = orders.map(order => {
        const steps   = ['pending', 'preparing', 'ready', 'delivered'];
        const stepIdx = steps.indexOf(order.status);
        const stepsHtml = steps.map((s, i) => `
          <div class="track-step ${i <= stepIdx ? 'done' : ''} ${i === stepIdx ? 'active' : ''}">
            <div class="track-step-dot"></div>
            <div class="track-step-label">${s.charAt(0).toUpperCase() + s.slice(1)}</div>
          </div>`).join('');

        const canCancel = order.status === 'pending';

        return `
          <div class="track-order-card">
            <div class="track-order-top">
              <div>
                <div class="track-order-name">${escHtml(order.menu_name)}</div>
                <div class="track-order-meta">Qty: ${order.quantity} &nbsp;·&nbsp; NRs.${Number(order.total_price).toFixed(0)} &nbsp;·&nbsp; Table ${order.table_number}</div>
                ${order.note ? `<div class="track-order-note">📝 ${escHtml(order.note)}</div>` : ''}
              </div>
              <span class="track-status-badge track-status-${order.status}">${order.status}</span>
            </div>
            <div class="track-steps">${stepsHtml}</div>
            ${canCancel ? `
              <div class="track-cancel-wrap" id="cancel-wrap-${order.id}">
                <input type="text" class="track-cancel-input" id="cancel-remark-${order.id}" placeholder="Reason for cancellation (optional)">
                <button class="track-cancel-btn" onclick="cancelOrder(${order.id})">Cancel Order</button>
              </div>` : ''}
          </div>`;
      }).join('');

      if (!trackInterval) {
        trackInterval = setInterval(refreshOrderStatus, 15000);
      }
    } catch (e) {
      console.error('Track order error:', e);
    }
  }

  window.cancelOrder = async function (orderId) {
    const remark  = document.getElementById(`cancel-remark-${orderId}`)?.value || '';
    const confirm = window.confirm('Are you sure you want to cancel this order?');
    if (!confirm) return;
    try {
      const data = await apiFetch('/api/orders/cancel', 'POST', { order_id: orderId, remark });
      if (data.success) {
        showToast('Order cancelled.');
        await refreshOrderStatus();
      } else {
        showToast(data.message || 'Could not cancel order.');
      }
    } catch (e) {
      showToast('Failed to cancel order.');
    }
  };

  //Track Order Button Visibility
  function showTrackOrderBtn() {
    const isMobile = window.innerWidth <= 768;
    const fullBtn  = document.getElementById('track-order-btn');
    const iconBtn  = document.getElementById('track-order-hamburger');
    if (fullBtn)  fullBtn.style.display  = isMobile ? 'none' : 'flex';
    if (iconBtn)  iconBtn.style.display  = isMobile ? 'flex' : 'none';
  }

  function hideTrackOrderBtn() {
    document.getElementById('track-order-btn')?.style.setProperty('display', 'none');
    document.getElementById('track-order-hamburger')?.style.setProperty('display', 'none');
  }

  async function checkActiveOrders() {
    if (!customerUUID) return;
    try {
      const data   = await apiFetch(`/api/orders/track?uuid=${encodeURIComponent(customerUUID)}`);
      const orders = data.orders || [];
      orders.length ? showTrackOrderBtn() : hideTrackOrderBtn();
    } catch (e) {
      console.error('Failed to check active orders:', e);
    }
  }

  // Re-evaluate button on resize (desktop ↔ mobile swap)
  window.addEventListener('resize', () => {
    const isVisible = document.getElementById('track-order-btn')?.style.display !== 'none'
                   || document.getElementById('track-order-hamburger')?.style.display !== 'none';
    if (isVisible) showTrackOrderBtn();
  }, { passive: true });

  //Init
  let customerUUID = null;

  (async () => {
    const now    = Date.now();
    const stored = localStorage.getItem(UUID_KEY);
    const expiry = localStorage.getItem(UUID_EXPIRY);

    if (stored && expiry && now < parseInt(expiry)) {
      customerUUID = stored;
      await loadCartFromDB();
      await checkActiveOrders();
    }
  })();

  checkoutForm?.classList.remove("active");
});