<?php
/**
 * Shared <head> + header/nav, session-aware.
 * Pages set $page_title / $meta_description (required) and optionally
 * $canonical_path, $og_image, $structured_data before requiring this file.
 */

$page_title       = $page_title ?? SITE_NAME;
$meta_description = $meta_description ?? 'Anchorline Logistics: freight forwarding, warehousing and last-mile delivery from Port Botany, NSW.';
// Both take a BASE_URL-relative path, e.g. "/index.php" or "/img/port-terminal.svg".
$canonical_path   = $canonical_path ?? '/' . basename($_SERVER['SCRIPT_NAME']);
$og_image         = $og_image ?? '/img/port-terminal.svg';

$current  = basename($_SERVER['SCRIPT_NAME']);
$user     = current_user();

$nav_links = [
    'index.php'    => 'Home',
    'services.php' => 'Services',
    'track.php'    => 'Track',
    'gallery.php'  => 'Gallery',
    'about.php'    => 'About',
    'contact.php'  => 'Contact',
];
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($page_title); ?> | <?php echo e(SITE_NAME); ?></title>
  <meta name="description" content="<?php echo e($meta_description); ?>">
  <link rel="canonical" href="<?php echo e(SITE_URL . $canonical_path); ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?php echo e(SITE_NAME); ?>">
  <meta property="og:title" content="<?php echo e($page_title); ?> | <?php echo e(SITE_NAME); ?>">
  <meta property="og:description" content="<?php echo e($meta_description); ?>">
  <meta property="og:image" content="<?php echo e(SITE_URL . $og_image); ?>">
  <meta name="robots" content="<?php echo e($robots ?? 'index, follow'); ?>">
  <link rel="icon" href="<?php echo e(BASE_URL); ?>/img/logo.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800&amp;family=IBM+Plex+Mono:wght@400;500&amp;family=IBM+Plex+Sans:wght@400;500;600&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo e(BASE_URL); ?>/css/style.css">
  <?php if (!empty($structured_data)): ?>
  <script type="application/ld+json"><?php echo $structured_data; ?></script>
  <?php endif; ?>
</head>
<body>
  <a class="skip-link" href="#main">Skip to main content</a>

  <header class="site-header">
    <div class="wrap">
      <a class="brand" href="<?php echo e(BASE_URL); ?>/index.php">
        <img src="<?php echo e(BASE_URL); ?>/img/logo.svg" alt="" width="34" height="34">
        Anchorline <span>Logistics</span>
      </a>
      <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu</button>
      <nav class="site-nav" id="site-nav" aria-label="Main">
        <ul>
          <?php foreach ($nav_links as $href => $label): ?>
          <li><a href="<?php echo e(BASE_URL); ?>/<?php echo e($href); ?>"<?php echo $current === $href ? ' aria-current="page"' : ''; ?>><?php echo e($label); ?></a></li>
          <?php endforeach; ?>
          <?php if ($user): ?>
          <li class="nav-auth-item"><a href="<?php echo e(BASE_URL); ?>/dashboard.php"<?php echo $current === 'dashboard.php' ? ' aria-current="page"' : ''; ?>><?php echo e($user['name']); ?> &middot; Dashboard</a></li>
          <li><a href="<?php echo e(BASE_URL); ?>/account.php"<?php echo $current === 'account.php' ? ' aria-current="page"' : ''; ?>>My account</a></li>
          <?php if ($user['role'] === 'admin'): ?>
          <li><a href="<?php echo e(BASE_URL); ?>/admin/consignments.php">Admin</a></li>
          <?php endif; ?>
          <li><a href="<?php echo e(BASE_URL); ?>/logout.php">Log out</a></li>
          <?php else: ?>
          <li class="nav-auth-item"><a href="<?php echo e(BASE_URL); ?>/login.php"<?php echo $current === 'login.php' ? ' aria-current="page"' : ''; ?>>Log in</a></li>
          <li><a href="<?php echo e(BASE_URL); ?>/register.php"<?php echo $current === 'register.php' ? ' aria-current="page"' : ''; ?>>Register</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <main id="main">
    <?php foreach (flash_get() as $flash): ?>
    <div class="wrap mt-2">
      <p class="feedback feedback--<?php echo $flash['type'] === 'error' ? 'error' : 'ok'; ?>" role="status"><?php echo e($flash['message']); ?></p>
    </div>
    <?php endforeach; ?>
