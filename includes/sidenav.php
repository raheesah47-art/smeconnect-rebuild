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
  <a href="/smeconnect/wishlist.php" class="nav-item <?= $active === 'wishlist' ? 'active' : '' ?>">♡ Wishlist</a>
  <a href="/smeconnect/seller-dashboard.html" id="sidenavDashboardLink" class="nav-item" style="display:none;">⚙ Seller Dashboard</a>


  <div class="seller-promo-card">
    <span class="seller-promo-label">SELLER PROGRAMME</span>
    <h3 class="seller-promo-title">Sell on SMEConnect</h3>
    <p class="seller-promo-text">Reach buyers island-wide. No listing fees for your first 90 days.</p>
    <button type="button" id="becomeSellerBtn" class="seller-promo-btn">Become a seller</button>
  </div>
</aside>