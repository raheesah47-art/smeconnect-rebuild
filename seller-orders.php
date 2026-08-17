<?php $active = ''; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

  <main>
    <div class="page-layout">
      <?php include 'includes/sidenav.php'; ?>

      <div class="main-content">
        <h2 class="section-title">Manage Orders</h2>
        <div id="sellerOrdersList" style="margin-top:20px;"></div>
      </div>

      <?php include 'includes/cart-sidebar.php'; ?>
    </div>
  </main>

  <?php include 'includes/modals.php'; ?>
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>
  <script src="/smeconnect/assets/js/seller-orders.js"></script>
</body>
</html>
