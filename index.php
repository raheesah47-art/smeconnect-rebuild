<?php $active = 'home'; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

  <main>
    <div class="page-layout">
      <?php include 'includes/sidenav.php'; ?>

      <div class="main-content">
        <section class="hero-banner">
          <p class="hero-tag">● 480+ local sellers · island-wide</p>
          <h1>Shop local. <span class="hero-highlight">Support growth.</span></h1>
          <p class="hero-sub">Fresh produce, handmade crafts and everyday finds — from real sellers in your district, delivered fast.</p>
          <a href="#productsSection" class="hero-cta">Start exploring →</a>
        </section>

        <section class="category-row">
          <a href="/smeconnect/categories.php?category=Fashion" class="category-item"><span>👕</span><p>Fashion</p></a>
          <a href="/smeconnect/categories.php?category=Beauty" class="category-item"><span>💧</span><p>Beauty</p></a>
          <a href="/smeconnect/categories.php?category=Electronics" class="category-item"><span>💼</span><p>Electronics</p></a>
          <a href="/smeconnect/categories.php?category=Home%20%26%20craft" class="category-item"><span>🏠</span><p>Home & craft</p></a>
          <a href="/smeconnect/categories.php?category=Produce" class="category-item"><span>🥭</span><p>Produce</p></a>
          <a href="/smeconnect/categories.php" class="category-item"><span>▦</span><p>All categories</p></a>
        </section>

        <section class="promo-row">
          <div class="promo-card promo-coral">
            <span class="promo-badge">FLASH SALE</span>
            <h3>Up to 40% off</h3>
            <p>Ends in 6h 12m →</p>
          </div>
          <div class="promo-card promo-teal">
            <span class="promo-badge">FREE DELIVERY</span>
            <h3>On orders over Rs 1,500</h3>
            <p>Any district, any seller →</p>
          </div>
          <div class="promo-card promo-gold">
            <span class="promo-badge">NEW SELLERS</span>
            <h3>12 makers joined this week</h3>
            <p>Discover their shops →</p>
          </div>
        </section>

        <section id="productsSection">
          <h2 class="section-title">Best deals for you</h2>
          <div id="productGrid" class="product-grid"></div>
        </section>

        <section class="makers-section">
          <div class="makers-header">
            <div>
              <h2 class="section-title">Meet the makers</h2>
              <p class="section-sub">Every shop on SMEConnect is trust-scored and ID-verified</p>
            </div>
            <a href="/smeconnect/makers.php" class="view-all-link">View all →</a>
          </div>
          <div id="makersGrid" class="makers-grid"></div>
        </section>

        <section class="trust-bar">
          <div class="trust-item"><span>🛡</span><div><strong>Secure payment</strong><p>PayPal & local cards</p></div></div>
          <div class="trust-item"><span>↺</span><div><strong>Easy returns</strong><p>7-day return window</p></div></div>
          <div class="trust-item"><span>💬</span><div><strong>Local support</strong><p>Real people, Kreol or English</p></div></div>
          <div class="trust-item"><span>★</span><div><strong>Trust scored</strong><p>Every seller verified</p></div></div>
        </section>
      </div>

      <?php include 'includes/cart-sidebar.php'; ?>
    </div>
  </main>

  <?php include 'includes/modals.php'; ?>
  <?php include 'includes/scripts.php'; ?>
  <script>loadProducts();</script>
</body>
</html>