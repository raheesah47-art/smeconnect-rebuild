<?php $active = 'best-sellers'; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

  <main>
    <div class="page-layout">
      <?php include 'includes/sidenav.php'; ?>
      <div class="main-content">
        <section id="productsSection">
          <h2 class="section-title">Best Sellers</h2>
          <div id="productGrid" class="product-grid"></div>
        </section>
      </div>
      <?php include 'includes/cart-sidebar.php'; ?>
    </div>
  </main>

  <?php include 'includes/modals.php'; ?>
  <?php include 'includes/scripts.php'; ?>
  <script>loadProducts('best-sellers');</script>
</body>
</html>
