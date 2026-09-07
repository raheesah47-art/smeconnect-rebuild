<?php $active = 'makers'; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

  <main>
    <div class="page-layout">
      <?php include 'includes/sidenav.php'; ?>
      <div class="main-content">
        <section id="makerProfileSection">
          <div id="makerProfileHeader"></div>
        </section>

        <section>
          <h2 class="section-title">Products from this maker</h2>
          <div id="makerProductsGrid" class="product-grid"></div>
        </section>
      </div>
      <?php include 'includes/cart-sidebar.php'; ?>
    </div>
  </main>

  <?php include 'includes/modals.php'; ?>
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>
  <script src="/smeconnect/assets/js/maker-detail.js"></script>
</body>
</html>