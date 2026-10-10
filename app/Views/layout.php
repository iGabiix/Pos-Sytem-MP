<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123e2d">
    <title><?= esc($title ?? 'Campus Store') ?> · Tamaraw POS</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/app.css') ?>">
    <script defer src="<?= base_url('assets/app.js') ?>"></script>
</head>
<body>
<?php $segment = service('uri')->getSegment(1); $isSale = $segment === 'sales' && service('uri')->getSegment(2) === 'new'; ?>
<aside class="sidebar" id="sidebar">
    <a class="brand" href="<?= site_url() ?>"><span class="brand-mark">T<span>✦</span></span><span>TAMARAW<small>CAMPUS STORE</small></span></a>
    <div class="sidebar-label">WORKSPACE</div>
    <nav aria-label="Main navigation">
        <?php foreach ([
            ['', 'grid', 'Overview', $segment === ''],
            ['sales/new', 'cart', 'Record sale', $isSale],
            ['products', 'box', 'Products', $segment === 'products'],
            ['customers', 'users', 'Customers', $segment === 'customers'],
            ['staff', 'staff', 'Staff', $segment === 'staff'],
            ['sales', 'receipt', 'Sales history', $segment === 'sales' && !$isSale],
        ] as [$href, $ico, $label, $active]): ?>
        <a href="<?= site_url($href) ?>" class="nav-link <?= $active ? 'active' : '' ?>" <?= $active ? 'aria-current="page"' : '' ?>><?= icon($ico) ?><span><?= $label ?></span><?= $active ? '<span class="nav-dot"></span>' : '' ?></a>
        <?php endforeach ?>
    </nav>
    <div class="sidebar-bottom">
        <div class="school-note"><span class="gold-line"></span><strong>In the service of<br>the Tamaraw community.</strong><span>FEU TECH INSPIRED</span></div>
        <div class="sidebar-user">
            <?php if (session('avatar')): ?><img class="avatar" src="<?= image_url(session('avatar')) ?>" alt=""><?php else: ?><span class="avatar"><?= esc(initials(session('full_name') ?? 'Staff')) ?></span><?php endif ?>
            <span><strong><?= esc(session('full_name')) ?></strong><small>Store staff</small></span>
            <form method="post" action="<?= site_url('logout') ?>"><?= csrf_field() ?><button class="icon-button logout" aria-label="Sign out" title="Sign out"><?= icon('logout') ?></button></form>
        </div>
    </div>
</aside>
<div class="shell">
    <header class="topbar">
        <div class="breadcrumb"><button type="button" class="icon-button menu-toggle" aria-label="Toggle navigation" aria-controls="sidebar" aria-expanded="false"><?= icon('menu') ?></button><span>Campus Store</span><?= icon('chevron') ?><strong><?= esc($title) ?></strong></div>
        <div class="topbar-right"><span class="store-status"><i></i> Store workspace</span><span class="topbar-divider"></span><?= icon('calendar') ?><time datetime="<?= date('Y-m-d') ?>"><?= date('M d, Y') ?></time></div>
    </header>
    <main id="main">
        <?= $this->include('partials/alerts') ?>
        <?= $this->renderSection('content') ?>
    </main>
    <footer class="page-footer"><span>Tamaraw Campus Store</span><span>Built for the green & gold. <span class="footer-dot">✦</span></span></footer>
</div>
<dialog id="confirm-dialog">
    <form method="dialog"><div class="dialog-icon"><?= icon('alert') ?></div><h2 id="confirm-title">Remove this record?</h2><p id="confirm-message"></p><div class="form-actions"><button class="button secondary" value="cancel">Cancel</button><button class="button danger" value="confirm">Confirm</button></div></form>
</dialog>
</body>
</html>

