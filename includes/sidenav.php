<?php $active = $active ?? ''; ?>
<aside class="side-nav">
  <a href="/smeconnect/index.php" class="nav-item <?= $active === 'home' ? 'active' : '' ?>">🏠 Home</a>
  <a href="/smeconnect/categories.php" class="nav-item <?= $active === 'categories' ? 'active' : '' ?>">▦ Categories</a>
  <a href="/smeconnect/deals.php" class="nav-item <?= $active === 'deals' ? 'active' : '' ?>">💳 Deals</a>
  <a href="/smeconnect/new-arrivals.php" class="nav-item <?= $active === 'new' ? 'active' : '' ?>">⭐ New arrivals</a>
  <a href="/smeconnect/best-sellers.php" class="nav-item <?= $active === 'bestsellers' ? 'active' : '' ?>">📊 Best sellers</a>
  <a href="/smeconnect/makers.php" class="nav-item <?= $active === 'makers' ? 'active' : '' ?>">📍 Local makers</a>
  <hr>
  <a href="/smeconnect/my-orders.html" class="nav-item">📦 My orders</a>
  <a href="/smeconnect/wishlist.html" class="nav-item">♡ Wishlist</a>
  <a href="/smeconnect/seller-dashboard.html" class="nav-item">⚙ Seller Dashboard</a>
</aside>