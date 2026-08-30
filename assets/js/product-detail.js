const params = new URLSearchParams(window.location.search);
const productId = params.get('id');
let currentQty = 1;

fetch(`${API_PRODUCTS}/get_product.php?id=${productId}`, { credentials: 'same-origin' })
  .then(res => res.json())
  .then(p => {
    const container = document.getElementById('productDetail');

    if (p.error) {
      container.innerHTML = `<p>${p.error}</p>`;
      return;
    }

    const discountPct = p.original_price
      ? Math.round(100 - (p.price / p.original_price) * 100)
      : null;

    container.innerHTML = `
      <p class="breadcrumb"><a href="/smeconnect/index.php">Home</a> / <a href="/smeconnect/categories.php?category=${encodeURIComponent(p.category)}">${p.category}</a> / ${p.name}</p>

      <div class="detail-card">
        <div class="detail-image">
          <img src="${p.image_url || 'https://placehold.co/400x400/CCCCCC/FFFFFF?text=No+Image'}" alt="${p.name}" class="detail-product-image">
          ${discountPct ? `<span class="badge-discount">-${discountPct}%</span>` : ''}
          <span class="badge-verified">${p.trust_score} Verified</span>
        </div>

        <div class="detail-info">
          <p class="category">${p.category}</p>
          <h1>${p.name}</h1>
          <p class="detail-seller">by <a href="/smeconnect/makers.php">${p.seller_name}</a> · ${p.district}</p>

          <div class="detail-price-row">
            <span class="detail-price">Rs ${p.price}</span>
            ${p.original_price ? `<span class="detail-original-price">Rs ${p.original_price}</span>` : ''}
            ${discountPct ? `<span class="detail-save-badge">Save ${discountPct}%</span>` : ''}
          </div>

          <div style="display:flex; align-items:center; margin-bottom:24px;">
            <div class="qty-stepper">
              <button id="qtyMinus">−</button>
              <span id="qtyValue">1</span>
              <button id="qtyPlus">+</button>
            </div>
            <button id="addToCartDetailBtn" class="btn-pill-primary">Add to cart</button>
          </div>

          <div class="detail-meta">
            <div class="detail-meta-item">
              <strong>Trust score</strong>
              <div class="trust-score-bar">
                <div class="trust-score-track"><div class="trust-score-fill" style="width:${p.trust_score}%"></div></div>
                <span class="trust-score-num">${p.trust_score}</span>
              </div>
            </div>
            <div class="detail-meta-item">
              <strong>District</strong>
              ${p.district}
            </div>
            <div class="detail-meta-item">
              <strong>Category</strong>
              ${p.category}
            </div>
            <div class="detail-meta-item">
              <strong>Seller</strong>
              ${p.seller_name}
            </div>
          </div>
        </div>
      </div>
    `;

    document.getElementById('qtyMinus').addEventListener('click', () => {
      if (currentQty > 1) currentQty--;
      document.getElementById('qtyValue').textContent = currentQty;
    });
    document.getElementById('qtyPlus').addEventListener('click', () => {
      currentQty++;
      document.getElementById('qtyValue').textContent = currentQty;
    });

    document.getElementById('addToCartDetailBtn').addEventListener('click', () => {
      for (let i = 0; i < currentQty; i++) {
        addToCart(p.id);
      }
    });
  })
  .catch(err => console.error('Failed to load product:', err));