<?php $active = 'makers'; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

  <main>
    <div class="page-layout">
      <?php include 'includes/sidenav.php'; ?>
      <div class="main-content">
        <section>
          <h2 class="section-title">Local Makers</h2>
          <div id="makersGrid" class="makers-grid"></div>
        </section>
      </div>
      <?php include 'includes/cart-sidebar.php'; ?>
    </div>
  </main>

  <?php include 'includes/modals.php'; ?>
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>
</body>
</html>