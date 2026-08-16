<?php $active = 'categories'; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

  <main>
    <div class="page-layout">
      <?php include 'includes/sidenav.php'; ?>

      <div class="main-content">
        <section class="category-row">
          <a href="/smeconnect/categories.php?category=Fashion" class="category-item"><span>👕</span><p>Fashion</p></a>
          <a href="/smeconnect/categories.php?category=Beauty" class="category-item"><span>💧</span><p>Beauty</p></a>
          <a href="/smeconnect/categories.php?category=Electronics" class="category-item"><span>💼</span><p>Electronics</p></a>
          <a href="/smeconnect/categories.php?category=Home%20%26%20craft" class="category-item"><span>🏠</span><p>Home & craft</p></a>
          <a href="/smeconnect/categories.php?category=Produce" class="category-item"><span>🥭</span><p>Produce</p></a>
          <a href="/smeconnect/categories.php" class="category-item"><span>▦</span><p>All categories</p></a>
        </section>

        <section id="productsSection">
          <h2 class="section-title">Categories</h2>
          <div id="productGrid" class="product-grid"></div>
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