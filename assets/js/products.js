const API_BASE = '/smeconnect/api';
const API_PRODUCTS = `${API_BASE}/products`;
const API_CART = `${API_BASE}/cart`;
const API_PAYMENTS = `${API_BASE}/payments`;
const API_WISHLIST = `${API_BASE}/wishlist`;

async function apiJson(url, options = {}) {
  const response = await fetch(url, {
    credentials: 'same-origin',
    ...options,
    headers: {
      'Content-Type': 'application/json',
      ...(options.headers || {})
    }
  });

  const text = await response.text();
  let data;

  try {
    data = text ? JSON.parse(text) : {};
  } catch (error) {
    throw new Error(`Invalid server response (${response.status}): ${text.substring(0, 200)}`);
  }

  if (!response.ok || data.error) {
    throw new Error(data.error || `Request failed (${response.status})`);
  }

  return data;
}

function loadProducts(overrideFilter, overrideCategory, overrideSearch) {
  const params = new URLSearchParams(window.location.search);
  const filter = overrideFilter !== undefined ? overrideFilter : (params.get('filter') || '');
  const category = overrideCategory !== undefined ? overrideCategory : (params.get('category') || '');
  const search = overrideSearch !== undefined ? overrideSearch : '';

  const url = `${API_PRODUCTS}/get_products.php?filter=${encodeURIComponent(filter)}&category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}`;

  apiJson(url)
    .then(products => {
      const grid = document.getElementById('productGrid');
      if (!grid) return;
      grid.innerHTML = '';

      const titleEl = document.querySelector('#productsSection .section-title');
      const titles = { deals: 'Deals', new: 'New arrivals', bestsellers: 'Best sellers' };
      if (titleEl) titleEl.textContent = category ? category : (titles[filter] || 'Best deals for you');

      if (!Array.isArray(products) || products.length === 0) {
        grid.innerHTML = '<p>No products found.</p>';
        return;
      }

      products.forEach(p => {
        const discountPct = p.original_price
          ? Math.round(100 - (p.price / p.original_price) * 100)
          : null;

        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
          <a href="/smeconnect/product.php?id=${encodeURIComponent(p.id)}" style="text-decoration:none;color:inherit;">
            <div class="card-tile">
              ${discountPct ? `<span class="badge-discount">-${discountPct}%</span>` : ''}
              <span class="badge-verified">${p.trust_score} Verified</span>
              <button type="button" class="wishlist-btn" data-id="${p.id}">♡</button>
            </div>
            <div class="card-body">
              <p class="category">${p.category}</p>
              <h4>${p.name}</h4>
              <p class="seller">by ${p.seller_name} · ${p.district}</p>
              <div class="price-row">
                <span class="price">Rs ${p.price}</span>
                ${p.original_price ? `<span class="original-price">Rs ${p.original_price}</span>` : ''}
                <button type="button" class="add-btn" data-id="${p.id}">+</button>
              </div>
            </div>
          </a>
        `;
        grid.appendChild(card);
      });

      grid.querySelectorAll('.add-btn').forEach(btn => {
        btn.addEventListener('click', async event => {
          event.preventDefault();
          event.stopPropagation();
          await addToCart(btn.dataset.id, btn);
        });
      });

      grid.querySelectorAll('.wishlist-btn').forEach(btn => {
        btn.addEventListener('click', event => {
          event.preventDefault();
          event.stopPropagation();
          toggleWishlist(btn.dataset.id, btn);
        });
      });
    })
    .catch(error => {
      console.error('loadProducts failed:', error);
      const grid = document.getElementById('productGrid');
      if (grid) grid.innerHTML = '<p>Unable to load products. Please try again.</p>';
    });
}

async function addToCart(productId, button = null) {
  if (!productId) {
    console.error('addToCart: missing product ID');
    return;
  }

  if (button) {
    button.disabled = true;
    button.dataset.originalText = button.textContent;
    button.textContent = '…';
  }

  try {
    const result = await apiJson(`${API_CART}/add_to_cart.php`, {
      method: 'POST',
      body: JSON.stringify({ product_id: Number(productId) })
    });

    if (!result.success) {
      throw new Error(result.error || 'Product could not be added to cart.');
    }

    await loadCart();

    if (button) {
      button.textContent = '✓';
      setTimeout(() => {
        button.textContent = button.dataset.originalText || '+';
        button.disabled = false;
      }, 900);
    }
  } catch (error) {
    console.error('addToCart failed:', error);
    alert(`Could not add this product to your cart.\n\n${error.message}`);
    if (button) {
      button.textContent = button.dataset.originalText || '+';
      button.disabled = false;
    }
  }
}

async function updateCartQty(cartItemId, quantity) {
  try {
    await apiJson(`${API_CART}/update_cart.php`, {
      method: 'POST',
      body: JSON.stringify({ cart_item_id: Number(cartItemId), quantity: Number(quantity) })
    });
    await loadCart();
  } catch (error) {
    console.error('updateCartQty failed:', error);
    alert(`Could not update the cart.\n\n${error.message}`);
  }
}

async function removeFromCart(cartItemId) {
  try {
    await apiJson(`${API_CART}/remove_from_cart.php`, {
      method: 'POST',
      body: JSON.stringify({ cart_item_id: Number(cartItemId) })
    });
    await loadCart();
  } catch (error) {
    console.error('removeFromCart failed:', error);
    alert(`Could not remove the item.\n\n${error.message}`);
  }
}

async function loadCart() {
  try {
    const items = await apiJson(`${API_CART}/get_cart.php`);
    const container = document.getElementById('cartItems');
    const countEl = document.getElementById('cartCount');
    const subtotalEl = document.getElementById('cartSubtotal');

    if (!container || !countEl || !subtotalEl) return;

    container.innerHTML = '';
    let subtotal = 0;
    let totalQty = 0;

    if (!Array.isArray(items) || items.length === 0) {
      container.innerHTML = '<p class="empty-cart">Your cart is empty.</p>';
    } else {
      items.forEach(item => {
        const price = Number(item.price) || 0;
        const quantity = Number(item.quantity) || 0;
        subtotal += price * quantity;
        totalQty += quantity;

        const row = document.createElement('div');
        row.className = 'cart-item';
        row.innerHTML = `
          <div>
            <p class="cart-item-name">${item.name}</p>
            <p class="cart-item-district">${item.district}</p>
          </div>
          <div class="qty-controls">
            <button type="button" class="qty-btn minus">-</button>
            <span>${quantity}</span>
            <button type="button" class="qty-btn plus">+</button>
          </div>
          <span class="cart-item-price">Rs ${(price * quantity).toFixed(2)}</span>
        `;

        row.querySelector('.minus').addEventListener('click', () => {
          if (quantity <= 1) {
            removeFromCart(item.cart_item_id);
          } else {
            updateCartQty(item.cart_item_id, quantity - 1);
          }
        });

        row.querySelector('.plus').addEventListener('click', () => {
          updateCartQty(item.cart_item_id, quantity + 1);
        });

        container.appendChild(row);
      });
    }

    countEl.textContent = totalQty;
    subtotalEl.textContent = `Rs ${subtotal.toFixed(2)}`;
    return items;
  } catch (error) {
    console.error('loadCart failed:', error);
    const container = document.getElementById('cartItems');
    if (container) container.innerHTML = '<p class="empty-cart">Unable to load cart.</p>';
    return [];
  }
}

async function toggleWishlist(productId, btn) {
  try {
    const result = await apiJson(`${API_WISHLIST}/toggle_wishlist.php`, {
      method: 'POST',
      body: JSON.stringify({ product_id: Number(productId) })
    });
    btn.textContent = result.wishlisted ? '♥' : '♡';
  } catch (error) {
    console.error('toggleWishlist failed:', error);
    alert(error.message);
  }
}

function loadMakers() {
  const makers = [
    { initials: 'AC', name: 'Atelier Coco', location: 'Grand Baie', trust: 98, tag: 'Textiles', color: '#E85D45' },
    { initials: 'FB', name: 'Ferme Bois Chéri', location: 'Moka', trust: 95, tag: 'Produce', color: '#3FA88C' },
    { initials: 'KD', name: 'Karo Design', location: 'Curepipe', trust: 91, tag: 'Home', color: '#D9A441' }
  ];
  const grid = document.getElementById('makersGrid');
  if (!grid) return;
  grid.innerHTML = makers.map(m => `
    <div class="maker-card">
      <div class="maker-avatar" style="background:${m.color}">${m.initials}</div>
      <div class="maker-info">
        <h4>${m.name}</h4>
        <p>${m.location} · Trust ${m.trust}</p>
        <div class="maker-tags"><span>${m.tag}</span></div>
      </div>
    </div>
  `).join('');
}

loadCart();
loadMakers();

if (typeof paypal !== 'undefined') {
  paypal.Buttons({
    createOrder: function () {
      return fetch(`${API_PAYMENTS}/paypal_create_order.php`, {
        method: 'POST',
        credentials: 'same-origin'
      })
        .then(res => res.json())
        .then(data => data.id);
    },
    onApprove: function (data) {
      return fetch(`${API_PAYMENTS}/paypal_capture_order.php`, {
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({ orderID: data.orderID })
      })
        .then(res => res.json())
        .then(result => {
          if (result.success) {
            alert(`Payment successful! Order #${result.order_id} placed.`);
            loadCart();
          } else {
            alert('Payment could not be completed.');
          }
        });
    }
  }).render('#paypal-button-container');
}