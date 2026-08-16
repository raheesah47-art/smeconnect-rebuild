const params = new URLSearchParams(window.location.search);
const productId = params.get('id');

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
      <div style="display:flex; gap:32px;">
        <div style="flex:1; height:320px; border-radius:12px; background:linear-gradient(135deg, #fbe4dd, #f8f1e0); position:relative;">
          ${discountPct ? `<span class="badge-discount">-${discountPct}%</span>` : ''}
          <span class="badge-verified">${p.trust_score} Verified</span>
        </div>
        <div style="flex:1;">
          <p class="category">${p.category}</p>
          <h1 style="font-family:var(--font-heading); margin:8px 0;">${p.name}</h1>
          <p style="color:#888;">by ${p.seller_name} · ${p.district}</p>

          <div style="margin:20px 0;">
            <span style="font-size:28px; font-weight:700;">Rs ${p.price}</span>
            ${p.original_price ? `<span class="original-price" style="font-size:18px; margin-left:10px;">Rs ${p.original_price}</span>` : ''}
          </div>

          <button id="addToCartDetailBtn" style="background:var(--color-coral); color:white; border:none; padding:14px 32px; border-radius:8px; font-weight:600; cursor:pointer; font-size:16px;">
            Add to cart
          </button>

          <div style="margin-top:24px; padding-top:24px; border-top:1px solid #eee;">
            <p><strong>Trust score:</strong> ${p.trust_score}/100</p>
            <p><strong>District:</strong> ${p.district}</p>
            <p><strong>Category:</strong> ${p.category}</p>
          </div>
        </div>
      </div>
    `;

    document.getElementById('addToCartDetailBtn').addEventListener('click', () => {
      addToCart(p.id);
      alert('Added to cart!');
    });
  })
  .catch(err => console.error('Failed to load product:', err));