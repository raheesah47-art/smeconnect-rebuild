<?php $active = ''; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

  <main>
    <div class="page-layout">
      <?php include 'includes/sidenav.php'; ?>

      <div class="main-content">
        <div id="productDetail" style="background:white; border-radius:16px; padding:32px;"></div>
      </div>

      <?php include 'includes/cart-sidebar.php'; ?>
    </div>
  </main>

  <?php include 'includes/modals.php'; ?>
  <?php include 'includes/scripts.php'; ?>
  <script src="/smeconnect/assets/js/product-detail.js"></script>
</body>
</html>