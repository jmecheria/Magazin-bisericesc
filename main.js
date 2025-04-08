// js/main.js

// Funcționalitate slider
let slideIndex = 0;
const slides = document.querySelectorAll('.slides img');
if (slides.length > 0) {
  function showSlides() {
    slides.forEach((slide) => {
      slide.classList.remove('active');
    });
    slideIndex++;
    if (slideIndex > slides.length) {
      slideIndex = 1;
    }
    slides[slideIndex - 1].classList.add('active');
    setTimeout(showSlides, 3000);
  }
  showSlides();
}

// Funcționalitate Coș
let cart = JSON.parse(localStorage.getItem('cart')) || [];

function updateCartCount() {
  const cartCount = document.getElementById('cart-count');
  if (cartCount) {
    let count = cart.reduce((acc, item) => acc + item.quantity, 0);
    cartCount.textContent = count;
  }
}

function addToCart(productName, price, quantity) {
  quantity = parseInt(quantity);
  let existingProduct = cart.find(item => item.name === productName);
  if (existingProduct) {
    existingProduct.quantity += quantity;
  } else {
    cart.push({ name: productName, price: price, quantity: quantity });
  }
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartCount();
  alert(productName + " a fost adăugat în coș.");
}

// Dacă suntem pe pagina de coș, afișăm conținutul
if (document.getElementById('cart-container')) {
  function displayCart() {
    const container = document.getElementById('cart-container');
    container.innerHTML = '';
    if (cart.length === 0) {
      container.innerHTML = '<p>Coșul este gol.</p>';
      return;
    }
    cart.forEach((item, index) => {
      const itemDiv = document.createElement('div');
      itemDiv.className = 'cart-item';
      itemDiv.innerHTML =
        '<span>' + item.name + '</span>' +
        '<input type="number" min="1" value="' + item.quantity + '" onchange="updateItemQuantity(' + index + ', this.value)">' +
        '<span>Preț: ' + (item.price * item.quantity) + ' RON</span>' +
        '<button onclick="removeItem(' + index + ')">Șterge</button>';
      container.appendChild(itemDiv);
    });
    updateTotal();
  }

  function updateItemQuantity(index, newQuantity) {
    newQuantity = parseInt(newQuantity);
    if (newQuantity <= 0) {
      removeItem(index);
    } else {
      cart[index].quantity = newQuantity;
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    displayCart();
  }

  function removeItem(index) {
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    displayCart();
  }

  function updateTotal() {
    const totalElement = document.getElementById('total-amount');
    let total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    totalElement.textContent = total;
  }
  
  // Afișăm conținutul coșului la încărcare
  displayCart();
}

// Trimiterea comenzii (simulare)
if (document.getElementById('order-form')) {
  document.getElementById('order-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const orderDetails = {
      customer_name: document.getElementById('customer-name').value,
      customer_phone: document.getElementById('customer-phone').value,
      customer_address: document.getElementById('customer-address').value,
      promo_code: document.getElementById('promo-code').value,
      cart: cart,
      total: cart.reduce((sum, item) => sum + item.price * item.quantity, 0)
    };
    console.log("Order Details:", orderDetails);
    alert("Comanda a fost trimisă. Veți primi un email de confirmare.");
    // Curățăm coșul după finalizare
    cart = [];
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    window.location.href = 'index.html';
  });
}

// Actualizează numărul de produse din coș la încărcare
updateCartCount();

