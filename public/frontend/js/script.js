/* ═══════════════════════════════════════════
   🍽️  BISTRO — Restaurant Menu Script
   ═══════════════════════════════════════════ */

document.addEventListener("DOMContentLoaded", () => {

  /* ── State ──────────────────────────────── */
  let cart = [];

  /* ── DOM Refs ───────────────────────────── */
  const cartBox       = document.getElementById("cart");
  const cartItemsEl   = document.getElementById("cart-items");
  const cartTotalEl   = document.getElementById("cart-total");
  const cartBadge     = document.querySelector(".cart-badge");
  const checkoutForm  = document.getElementById("checkout-form");
  const checkoutItems = document.getElementById("checkout-items");
  const backdrop      = document.getElementById("cart-backdrop");

  /* ── Header Scroll Effect ───────────────── */
  const header = document.querySelector("header");
  window.addEventListener("scroll", () => {
    header?.classList.toggle("scrolled", window.scrollY > 10);
  }, { passive: true });

  /* ── Toast ──────────────────────────────── */
  function showToast(message) {
    const container = document.querySelector(".toast-container") || createToastContainer();
    const toast = document.createElement("div");
    toast.className = "toast";
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 2600);
  }

  function createToastContainer() {
    const el = document.createElement("div");
    el.className = "toast-container";
    document.body.appendChild(el);
    return el;
  }

  /* ── Cart Visibility ────────────────────── */
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

  // Close cart on backdrop click
  backdrop?.addEventListener("click", closeCartFn);

  // Close cart on outside click (desktop)
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

  /* ── Add to Cart ────────────────────────── */
  window.addToCart = function (id, name, price) {
    const existing = cart.find(i => i.id === id);
    if (existing) {
      existing.qty++;
    } else {
      cart.push({ id, name, price: parseFloat(price), qty: 1 });
    }

    // Button flash animation
    const btn = document.querySelector(`[data-item-id="${id}"]`);
    if (btn) {
      btn.classList.add("added");
      btn.textContent = "✓ Added";
      setTimeout(() => {
        btn.classList.remove("added");
        btn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
      }, 1200);
    }

    renderCart();
    openCart();
    showToast(`🛒 ${name} added to cart`);
  };

  /* ── Render Cart ────────────────────────── */
  function renderCart() {
    cartItemsEl.innerHTML = "";

    if (cart.length === 0) {
      cartItemsEl.innerHTML = `
        <div class="cart-empty">
          <span class="empty-icon">🍽️</span>
          <p>Your cart is empty</p>
        </div>`;
      cartTotalEl.textContent = "0.00";
      updateBadge(0);
      return;
    }

    let total = 0;
    let totalQty = 0;

    cart.forEach((item, idx) => {
      const itemTotal = item.price * item.qty;
      total += itemTotal;
      totalQty += item.qty;

      cartItemsEl.innerHTML += `
        <div class="cart-item">
          <div class="cart-item-info">
            <div class="cart-item-name">${escHtml(item.name)}</div>
            <div class="cart-item-price">NRs.${itemTotal.toFixed(0)}</div>
          </div>
          <div class="cart-actions">
            <button class="qty-btn" onclick="decreaseQty(${idx})" aria-label="Decrease">−</button>
            <span class="qty-display">${item.qty}</span>
            <button class="qty-btn" onclick="increaseQty(${idx})" aria-label="Increase">+</button>
            <button class="remove-btn" onclick="removeItem(${idx})" aria-label="Remove">✕</button>
          </div>
        </div>`;
    });

    cartTotalEl.textContent = total.toFixed(2);
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

  /* ── Cart Actions ───────────────────────── */
  window.increaseQty = function (idx) { cart[idx].qty++; renderCart(); };
  window.decreaseQty = function (idx) {
    if (cart[idx].qty > 1) cart[idx].qty--;
    else cart.splice(idx, 1);
    renderCart();
  };
  window.removeItem = function (idx) {
    const name = cart[idx].name;
    cart.splice(idx, 1);
    renderCart();
    showToast(`Removed ${name}`);
  };

  /* ── Checkout ───────────────────────────── */
  window.openCheckout = function () {
    if (!cart.length) { showToast("🛒 Your cart is empty!"); return; }

    checkoutItems.innerHTML = "";
    let total = 0;

    cart.forEach(item => {
      const lineTotal = item.price * item.qty;
      total += lineTotal;
      checkoutItems.innerHTML += `
        <p>
          <span>${escHtml(item.name)} × ${item.qty}</span>
          <span>NRs.${lineTotal.toFixed(0)}</span>
        </p>
        <input type="hidden" name="menu_ids[]" value="${item.id}">
        <input type="hidden" name="quantities[]" value="${item.qty}">`;
    });

    const totalEl = document.getElementById("checkout-total");
    if (totalEl) totalEl.textContent = total.toFixed(2);

    // Also update display span if exists
    const displayEl = document.getElementById("checkout-total-display");
    if (displayEl) displayEl.textContent = total.toFixed(2);

    checkoutForm.classList.add("active");
    closeCartFn();
  };

  window.closeCheckout = function () {
    checkoutForm.classList.remove("active");
  };

  // Close checkout on backdrop click
  checkoutForm?.addEventListener("click", function (e) {
    if (e.target === this) closeCheckout();
  });

  /* ── Category Filter ────────────────────── */
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
          // Re-trigger animation
          card.style.animation = "none";
          card.offsetHeight; // reflow
          card.style.animation = `card-reveal 0.5s var(--ease-spring) ${(i % 8) * 0.06}s forwards`;
        } else {
          card.style.display = "none";
        }
      });
    });
  });

  /* ── Mobile Menu Toggle ─────────────────── */
  window.toggleMobileMenu = function () {
    document.getElementById("category-bar")?.classList.toggle("active");
  };

  /* ── Escape key closes panels ───────────── */
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeCartFn();
      closeCheckout();
    }
  });

  /* ── Utility ────────────────────────────── */
  function escHtml(str) {
    return str
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  /* ── Initial Render ─────────────────────── */
  renderCart();
  checkoutForm?.classList.remove("active");
});