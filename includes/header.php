<?php
$_me = me();
$_pg = basename($_SERVER['SCRIPT_NAME'],'.php');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= $pageTitle ?? 'TravelNest — India\'s Travel Platform' ?></title>
<meta name="description" content="Book flights, hotels, trains, buses, cabs, cruises & holiday packages at best prices. TravelNest — India's trusted travel platform.">
<meta name="csrf" content="<?= csrf() ?>">
<meta name="base" content="<?= BASE ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE ?>/assets/css/style.css">
</head>
<body>
<div class="toast" id="toast"></div>

<!-- Mobile Drawer Overlay -->
<div class="nav-drawer-overlay" id="nav-overlay"></div>

<!-- Mobile Drawer -->
<div class="nav-drawer" id="nav-drawer">
  <button class="drawer-close" onclick="closeDrawer()">✕</button>
  <div style="font-family:'Inter',sans-serif;font-size:22px;font-weight:700;color:var(--accent);margin-bottom:20px">Travel<span style="color:var(--text)">Nest</span></div>
  <?php
  $mnavs = [
    'index'   => ['🏠','Home'],
    'flights' => ['✈️','Flights'],
    'hotels'  => ['🏨','Hotels'],
    'packages'=> ['📦','Packages'],
    'trains'  => ['🚆','Trains'],
    'buses'   => ['🚌','Buses'],
    'cabs'    => ['🚕','Cabs'],
    'cruises' => ['🚢','Cruises'],
  ];
  foreach($mnavs as $p => $l){
    $isOn = ($_pg === $p) ? ' on' : '';
    echo "<a class='$isOn' href='".BASE."/$p.php'>{$l[0]} {$l[1]}</a>";
  }
  ?>
  <div style="border-top:1px solid var(--border);margin:16px 0;padding-top:16px">
  <?php if($_me): ?>
    <?php if($_me['role'] === 'admin'): ?>
      <a href="<?= BASE ?>/admin/index.php">📊 Dashboard</a>
    <?php else: ?>
      <a href="<?= BASE ?>/wishlist.php">❤️ Wishlist</a>
      <a href="<?= BASE ?>/bookings.php">📋 My Bookings</a>
    <?php endif; ?>
    <a href="<?= BASE ?>/logout.php">🚪 Logout</a>
  <?php else: ?>
    <a href="<?= BASE ?>/login.php">🔑 Login</a>
    <a href="<?= BASE ?>/register.php">✨ Sign Up</a>
  <?php endif; ?>
  </div>
</div>

<nav class="main-nav">
  <a class="logo" href="<?= BASE ?>/index.php">Travel<span>Nest</span></a>
  <div class="nav-links">
    <?php
    $navs = [
      'index'   => 'Home',
      'flights' => 'Flights',
      'hotels'  => 'Hotels',
      'packages'=> 'Packages',
      'trains'  => 'Trains',
      'buses'   => 'Buses',
      'cabs'    => 'Cabs',
      'cruises' => 'Cruises',
    ];
    foreach($navs as $p => $l){
      $isOn = ($_pg === $p) || ($_pg === 'home' && $p === 'index');
      $cls  = $isOn ? 'nb on' : 'nb';
      echo "<a class='$cls' href='".BASE."/$p.php'>$l</a>";
    }
    ?>
  </div>
  <div class="nav-right">
    <?php if($_me): ?>
      <?php if($_me['role'] === 'admin'): ?>
        <span class="tag t-gold" style="font-size:11px">Admin</span>
        <a class="btn btn-primary btn-sm" href="<?= BASE ?>/admin/index.php">Dashboard</a>
        <a class="btn btn-ghost btn-sm"   href="<?= BASE ?>/logout.php">Logout</a>
      <?php else: ?>
        <span class="sm" style="color:var(--text2)">Hi, <?= clean(explode(' ',$_me['name'])[0]) ?></span>
        <a class="btn btn-ghost btn-sm" href="<?= BASE ?>/wishlist.php"  title="Wishlist">❤️</a>
        <a class="btn btn-ghost btn-sm" href="<?= BASE ?>/bookings.php">My Bookings</a>
        <a class="btn btn-ghost btn-sm" href="<?= BASE ?>/logout.php">Logout</a>
      <?php endif; ?>
    <?php else: ?>
      <a class="btn btn-ghost btn-sm"   href="<?= BASE ?>/login.php">Login</a>
      <a class="btn btn-primary btn-sm" href="<?= BASE ?>/register.php">Sign Up</a>
    <?php endif; ?>
    <button class="nav-toggle" onclick="openDrawer()" aria-label="Menu">☰</button>
  </div>
</nav>

<?= getFlash() ?>
