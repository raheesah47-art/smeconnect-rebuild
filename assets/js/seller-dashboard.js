const API_BASE = 'http://localhost/smeconnect/api/seller';

function loadMyProducts() {
  fetch(`${API_BASE}/get_my_products.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(products => {
      if (products.error) {
        document.querySelector('main').innerHTML = `<p>${products.error} — <a href="index.html">log in as a seller</a> first.</p>`;
        return;
      }
      const list = document.getElementById('myProductsList');
      list.innerHTML = '';
      products.forEach(p => {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
          <div class="card-body">
            <p class="category">${p.category}</p>
            <h4>${p.name}</h4>
            <p class="seller">${p.district}</p>
            <div class="price-row">
              <span class="price">Rs ${p.price}</span>
            </div>
            <button class="edit-btn" data-id="${p.id}">Edit</button>
            <button class="delete-btn" data-id="${p.id}">Delete</button>
          </div>
        `;
        list.appendChild(card);
      });

      document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => openEditForm(products.find(p => p.id == btn.dataset.id)));
      });
      document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => deleteProduct(btn.dataset.id));
      });
    });
}

function openEditForm(product) {
  document.getElementById('productForm').style.display = 'block';
  document.getElementById('formTitle').textContent = 'Edit Product';
  document.getElementById('editProductId').value = product.id;
  document.getElementById('pName').value = product.name;
  document.getElementById('pCategory').value = product.category;
  document.getElementById('pDistrict').value = product.district;
  document.getElementById('pPrice').value = product.price;
  document.getElementById('pOriginalPrice').value = product.original_price || '';
}

function deleteProduct(id) {
  if (!confirm('Delete this product?')) return;
  fetch(`${API_BASE}/delete_product.php`, {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify({ id })
  }).then(() => loadMyProducts());
}

document.getElementById('showAddFormBtn').addEventListener('click', () => {
  document.getElementById('productForm').style.display = 'block';
  document.getElementById('formTitle').textContent = 'Add Product';
  document.getElementById('editProductId').value = '';
  ['pName','pCategory','pDistrict','pPrice','pOriginalPrice'].forEach(id => document.getElementById(id).value = '');
});

document.getElementById('cancelFormBtn').addEventListener('click', () => {
  document.getElementById('productForm').style.display = 'none';
});

document.getElementById('saveProductBtn').addEventListener('click', () => {
  const id = document.getElementById('editProductId').value;
  const payload = {
    name: document.getElementById('pName').value,
    category: document.getElementById('pCategory').value,
    district: document.getElementById('pDistrict').value,
    price: parseFloat(document.getElementById('pPrice').value),
    original_price: parseFloat(document.getElementById('pOriginalPrice').value) || null
  };

  const endpoint = id ? 'update_product.php' : 'add_product.php';
  if (id) payload.id = id;

  fetch(`${API_BASE}/${endpoint}`, {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify(payload)
  })
    .then(res => res.json())
    .then(() => {
      document.getElementById('productForm').style.display = 'none';
      loadMyProducts();
    });
});

loadMyProducts();