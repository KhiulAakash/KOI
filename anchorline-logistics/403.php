<?php
require_once __DIR__ . '/config.php';
$page_title = 'Access denied';
$meta_description = 'You do not have permission to view this page.';
$robots = 'noindex, nofollow';
require ROOT_PATH . '/includes/header.php';
?>
<section class="page-head">
  <div class="wrap">
    <p class="eyebrow">Anchorline Logistics</p>
    <h1>Access denied</h1>
    <p>Your account does not have permission to view this page.</p>
  </div>
</section>
<section class="section">
  <div class="wrap">
    <p class="lede">If you think this is wrong, contact an administrator, or head back to
       your <a href="<?php echo e(BASE_URL); ?>/dashboard.php">dashboard</a>.</p>
  </div>
</section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
