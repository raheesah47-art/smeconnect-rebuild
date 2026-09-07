const API_MAKERS_DETAIL = 'http://localhost/smeconnect/api/makers';

function getSellerFromUrl() {
  const params = new URLSearchParams(window.location.search);
  return params.get('seller') || '';
}

function loadMakerDetail() {
  const sellerName = getSellerFromUrl();
  const headerEl = document.getElementById('makerProfileHeader');
  const gridEl = document.getElementById('makerProductsGrid');

  if (!sellerName) {
    headerEl.innerHTML = '<p>No maker specified.</p>';
    return;
  }

  fetch(`${API_MAKERS_DETAIL}/get_maker_detail.php?seller=${encodeURIComponent(sellerName)}`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        headerEl.innerHTML = `<p>${data.error}</p>`;
        return;
      }

      const m = data.maker;
      const avatarContent = m.profile_image
        ? `<img src="${m.profile_image}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">`
        : m.seller_name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();

      headerEl.innerHTML = `
        <div class="maker-profile-card">
          <div class="maker-avatar" style="width:80px; height:80px; font-size:28px;">${avatarContent}</div>
          <div>
            <h2>${m.seller_name}</h2>
            <p>${m.district || 'Mauritius'} · Trust Score ${m.avg_trust}</p>
            <p>${m.product_count} product${m.product_count == 1 ? '' : 's'} listed</p>
          </div>
        </div>
      `;

      if (!data.products.length) {
        gridEl.innerHTML = '<p>This maker has no products listed yet.</p>';
        return;
      }

      gridEl.innerHTML = data.products.map(p => {
        const discountPct = p.original_price
          ? Math.round(100 - (p.price / p.original_price) * 100)
          : null;

        return `
          <div class="product-card">
            <img src="${p.image}" alt="${p.name}">
            <h4>${p.name}</h4>
            <p class="product-price">Rs ${p.price}${discountPct ? ` <span class="discount">-${discountPct}%</span>` : ''}</p>
          </div>
        `;
      }).join('');
    })
    .catch(() => {
      headerEl.innerHTML = '<p>Something went wrong loading this maker.</p>';
    });
}

loadMakerDetail();