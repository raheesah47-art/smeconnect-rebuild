const API_ADMIN = 'http://localhost/smeconnect/api/admin';

function loadUsers() {
  fetch(`${API_ADMIN}/get_all_users.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(users => {
      if (users.error) {
        document.querySelector('.dash-main').innerHTML = `<p>${users.error}</p>`;
        return;
      }

      document.getElementById('usersTable').innerHTML = `
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>District</th><th>Status</th><th>Joined</th><th></th></tr></thead>
        <tbody>
          ${users.map(u => `
            <tr>
              <td>${u.name}</td>
              <td>${u.email}</td>
              <td><span class="status-pill status-confirmed">${u.role}</span></td>
              <td>${u.district || '—'}</td>
              <td><span class="status-pill ${u.is_active == 1 ? 'status-delivered' : 'status-out-for-delivery'}">${u.is_active == 1 ? 'Active' : 'Suspended'}</span></td>
              <td>${new Date(u.created_at).toLocaleDateString()}</td>
              <td><button class="admin-toggle-status" data-id="${u.id}" style="background:none; border:none; cursor:pointer; font-weight:600; color:${u.is_active == 1 ? '#c0392b' : 'var(--color-teal-dark)'};">${u.is_active == 1 ? 'Suspend' : 'Reactivate'}</button></td>
            </tr>
          `).join('')}
        </tbody>
      `;

      document.querySelectorAll('.admin-toggle-status').forEach(btn => {
        btn.addEventListener('click', () => {
          const action = btn.textContent.trim() === 'Suspend' ? 'suspend' : 'reactivate';
          if (!confirm(`Are you sure you want to ${action} this user?`)) return;

          fetch(`${API_ADMIN}/toggle_user_status.php`, {
            method: 'POST', credentials: 'same-origin',
            body: JSON.stringify({ id: btn.dataset.id })
          })
            .then(res => res.json())
            .then(result => {
              if (result.error) { alert(result.error); return; }
              loadUsers();
            });
        });
      });
    });
}

function loadProducts() {
  fetch(`${API_ADMIN}/get_all_products.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(products => {
      document.getElementById('productsTable').innerHTML = `
        <thead><tr><th>Photo</th><th>Name</th><th>Seller</th><th>Category</th><th>Price</th><th>Trust</th><th></th></tr></thead>
        <tbody>
          ${products.map(p => `
            <tr>
              <td>
                <img src="${p.image_url || 'https://placehold.co/60x60/CCCCCC/FFFFFF?text=No+Image'}" style="width:44px; height:44px; object-fit:cover; border-radius:6px;">
                <input type="file" class="admin-image-input" data-id="${p.id}" accept="image/jpeg,image/png,image/webp" style="display:block; font-size:11px; margin-top:4px; width:110px;">
              </td>
              <td>${p.name}</td>
              <td>${p.seller_name || '—'}</td>
              <td>${p.category}</td>
              <td>Rs ${p.price}</td>
              <td>${p.trust_score}</td>
              <td><button class="admin-delete-product" data-id="${p.id}" style="color:#c0392b; background:none; border:none; cursor:pointer; font-weight:600;">Remove</button></td>
            </tr>
          `).join('')}
        </tbody>
      `;

      document.querySelectorAll('.admin-delete-product').forEach(btn => {
        btn.addEventListener('click', () => {
          if (!confirm('Remove this product from the platform?')) return;
          fetch(`${API_ADMIN}/delete_product.php`, {
            method: 'POST', credentials: 'same-origin',
            body: JSON.stringify({ id: btn.dataset.id })
          }).then(() => loadProducts());
        });
      });

      document.querySelectorAll('.admin-image-input').forEach(input => {
        input.addEventListener('change', () => {
          const file = input.files[0];
          if (!file) return;
          const formData = new FormData();
          formData.append('id', input.dataset.id);
          formData.append('image', file);

          fetch(`${API_ADMIN}/upload_product_image.php`, {
            method: 'POST', credentials: 'same-origin', body: formData
          })
            .then(res => res.json())
            .then(result => {
              if (result.error) { alert(result.error); return; }
              loadProducts();
            });
        });
      });
    });
}

function loadOrders() {
  fetch(`${API_ADMIN}/get_all_orders.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(orders => {
      document.getElementById('ordersTable').innerHTML = `
        <thead><tr><th>Order ID</th><th>District</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          ${orders.map(o => `
            <tr>
              <td>#${o.id}</td>
              <td>${o.district}</td>
              <td>Rs ${o.total}</td>
              <td><span class="status-pill status-${(o.current_status || 'placed').toLowerCase().replace(/\s/g,'-')}">${o.current_status || 'Placed'}</span></td>
              <td>${new Date(o.created_at).toLocaleDateString()}</td>
            </tr>
          `).join('')}
        </tbody>
      `;
    });
}

loadUsers();
loadProducts();
loadOrders();