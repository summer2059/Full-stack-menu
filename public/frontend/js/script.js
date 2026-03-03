document.addEventListener("DOMContentLoaded", () => {

  const UUID_KEY    = 'bistro_customer_uuid';
  const UUID_EXPIRY = 'bistro_uuid_expiry';
  const TTL_MS      = 1 * 60 * 1000; // 1 minute (testing) increase 24 * 60 * 60 * 1000 for production

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
    scheduleExpiry();
    return uuid;
  }
  function scheduleExpiry(delay) {
  setTimeout(() => {
    localStorage.removeItem(UUID_KEY);
    localStorage.removeItem(UUID_EXPIRY);
    customerUUID = null;
    console.log("UUID expired and removed.");
  }, delay);
}

  let cart = []; // [{ cart_id, menu_id, name, price, quantity, total_price, is_select }]

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

  const header = document.querySelector("header");
  window.addEventListener("scroll", () => {
    header?.classList.toggle("scrolled", window.scrollY > 10);
  }, { passive: true });

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
      !e.target.closest(".cart-btn")
    ) {
      closeCartFn();
    }
  });

  window.addToCart = async function (id, name, price) {
    if (!customerUUID) {
      customerUUID = await getOrCreateUUID();
    }

    const existing = cart.find(i => i.menu_id === id);
    if (existing) {
      existing.quantity++;
      existing.total_price = existing.price * existing.quantity;
    } else {
      cart.push({ cart_id: null, menu_id: id, name, price: parseFloat(price), quantity: 1, total_price: parseFloat(price), is_select: 1 });
    }
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

    openCart();
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
            <button class="qty-btn" onclick="decreaseQty(${idx})" aria-label="Decrease">−</button>
            <span class="qty-display">${item.quantity}</span>
            <button class="qty-btn" onclick="increaseQty(${idx})" aria-label="Increase">+</button>
            <button class="remove-btn" onclick="removeItem(${idx})" aria-label="Remove">✕</button>
          </div>
        </div>`;
    });

    cartTotalEl.textContent = selectedTotal.toFixed(2);
    updateBadge(totalQty);
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

  window.increaseQty = async function (idx) {
    cart[idx].quantity++;
    cart[idx].total_price = cart[idx].price * cart[idx].quantity;
    renderCart();
    if (cart[idx].cart_id) {
      await apiFetch('/api/cart/update', 'POST', { cart_id: cart[idx].cart_id, quantity: cart[idx].quantity });
    }
  };

  window.decreaseQty = async function (idx) {
    if (cart[idx].quantity > 1) {
      cart[idx].quantity--;
      cart[idx].total_price = cart[idx].price * cart[idx].quantity;
      renderCart();
      if (cart[idx].cart_id) {
        await apiFetch('/api/cart/update', 'POST', { cart_id: cart[idx].cart_id, quantity: cart[idx].quantity });
      }
    } else {
      await removeItem(idx);
    }
  };

  window.removeItem = async function (idx) {
    const item = cart[idx];
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
// alert('cart id'+item.cart_id);
    if (item.cart_id) {
      try {
        await apiFetch('/api/cart/toggle-select', 'POST', {
          cart_id: item.cart_id,
          is_select: newVal,
        });
      } catch (e) {
        item.is_select = checked ? 0 : 1;
        const checkbox = document.querySelector(`#cart-item-${idx} .cart-checkbox`);
        if (checkbox) checkbox.checked = !checked;
        updateTotalDisplay();
        showToast('Failed to update selection');
      }
    }
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

    selectedItems.forEach(item => {
      const lineTotal = item.price * item.quantity;
      total += lineTotal;
      checkoutItems.innerHTML += `
        <p>
          <span>${escHtml(item.name)} × ${item.quantity}</span>
          <span>NRs.${lineTotal.toFixed(0)}</span>
        </p>`;
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

  if (document.querySelector('.alert-success, [data-flash="success"]')) {
    localStorage.removeItem(UUID_KEY);
    localStorage.removeItem(UUID_EXPIRY);
  }
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
    }
  });

  function escHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  let customerUUID = null;

  (async () => {
    const now    = Date.now();
    const stored = localStorage.getItem(UUID_KEY);
    const expiry = localStorage.getItem(UUID_EXPIRY);

    if (stored && expiry && now < parseInt(expiry)) {
      customerUUID = stored;
      await loadCartFromDB();
    }
  })();

  checkoutForm?.classList.remove("active");

});