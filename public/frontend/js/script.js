// Dark/Light Mode
const toggleBtn = document.querySelector(".toggle-mode");
toggleBtn.addEventListener("click", () => {
  document.body.classList.toggle("dark");
  toggleBtn.textContent = document.body.classList.contains("dark")
    ? "☀️ Light Mode"
    : "🌙 Dark Mode";
});

// Cart
const cartBtn = document.querySelector(".toggle-cart");
const cartBox = document.getElementById("cart");
cartBtn.addEventListener("click", () => cartBox.classList.toggle("active"));

let cart = [];
const cartItems = document.getElementById("cart-items");
const cartTotal = document.getElementById("cart-total");

function addToCart(name, price) {
  const item = cart.find(i => i.name === name);
  if (item) item.qty++;
  else cart.push({ name, price, qty: 1 });
  renderCart();
}

function renderCart() {
  cartItems.innerHTML = "";
  let total = 0;
  cart.forEach(item => {
    total += item.price * item.qty;
    cartItems.innerHTML += `
      <div class="cart-item">
        <span>${item.name} x${item.qty}</span>
        <span>$${(item.price * item.qty).toFixed(2)}</span>
      </div>`;
  });
  cartTotal.textContent = total.toFixed(2);
}

// Category filter
const categoryBar = document.getElementById("category-bar");
const menuCards = document.querySelectorAll(".card");
categoryBar.addEventListener("click", (e) => {
  if (!e.target.classList.contains("category-btn")) return;
  document.querySelectorAll(".category-btn").forEach(btn => btn.classList.remove("active"));
  e.target.classList.add("active");
  const category = e.target.dataset.category;
  menuCards.forEach(card => {
    card.style.display = (category === "all" || card.dataset.category === category) ? "block" : "none";
  });
});

// Checkout Form
const checkoutForm = document.getElementById("checkout-form");
document.querySelector(".checkout").addEventListener("click", () => {
  if (cart.length === 0) return alert("Cart is empty!");
  checkoutForm.classList.add("active");
});

function closeCheckout() { checkoutForm.classList.remove("active"); }

document.getElementById("order-form").addEventListener("submit", (e) => {
  e.preventDefault();
  const orderData = {
    name: document.getElementById("name").value,
    table: document.getElementById("table").value,
    phone: document.getElementById("phone").value,
    notes: document.getElementById("notes").value,
    cart: cart
  };
  console.log("Order Submitted:", orderData);
  alert("✅ Order placed! Waiter will serve you soon.");
  closeCheckout();
  cart = [];
  renderCart();
});
