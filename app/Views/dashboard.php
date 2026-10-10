<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="page-heading"><div><span class="eyebrow">YOUR STORE, AT A GLANCE</span><h1>Good <?= (int) date('G') < 12 ? 'morning' : ((int) date('G') < 18 ? 'afternoon' : 'evening') ?>, <?= esc(explode(' ', session('full_name'))[0]) ?> <span class="greeting-spark">✦</span></h1><p>Here’s what’s happening at your campus store today.</p></div><a class="button primary" href="<?= site_url('sales/new') ?>"><?= icon('plus') ?> Record a sale</a></div>

<section class="welcome-banner"><div><span class="eyebrow gold">CAMPUS SPIRIT. EVERY TRANSACTION.</span><h2>Great days start<br>with the green & gold.</h2><p>Keep your shelves ready and your community moving.</p><a class="banner-link" href="<?= site_url('products') ?>">Explore your inventory <?= icon('arrow') ?></a></div><div class="banner-art" aria-hidden="true"><span class="banner-circle"></span><img class="banner-shirt" src="<?= base_url('assets/products/sample-shirt.svg') ?>" alt=""><img class="banner-tumbler" src="<?= base_url('assets/products/sample-tumbler.svg') ?>" alt=""><span class="banner-stamp">CAMPUS<br>ESSENTIALS<span>✦</span></span></div></section>

<section class="stat-grid" aria-label="Store statistics">
<?php foreach ([
    ['wallet', 'Today’s revenue', money($today['revenue']), 'From completed sales', 'green'],
    ['receipt', 'Today’s transactions', $today['transactions'], 'Every sale, accounted for', 'gold'],
    ['box', 'Active products', $productsCount, $lowStockCount . ' need a stock check', 'blue'],
    ['users', 'Customers', $customersCount, 'Part of the campus community', 'purple'],
] as [$ico,$label,$value,$note,$color]): ?>
<article class="stat-card"><div class="stat-top"><span><?= $label ?></span><span class="stat-icon <?= $color ?>"><?= icon($ico) ?></span></div><strong><?= esc($value) ?></strong><small><?= esc($note) ?></small></article>
<?php endforeach ?>
</section>
<div class="dashboard-middle">
<section class="panel revenue-panel"><div class="panel-heading"><div><h2>Sales activity</h2><p>Your revenue over the past 7 days</p></div><span class="pill neutral"><?= icon('calendar') ?> Last 7 days</span></div>
<?php $max = max(1, ...array_column($days, 'total')); $sum = array_sum(array_column($days, 'total')); ?>
<div class="chart-summary"><strong><?= money($sum) ?></strong><span><i></i> Revenue</span></div>
<div class="chart" role="img" aria-label="Daily revenue: <?= esc(implode(', ', array_map(static fn($day) => $day['label'] . ' ' . money($day['total']), $days))) ?>">
<div class="chart-lines"><span><?= money($max) ?></span><span><?= money($max / 2) ?></span><span>₱0</span></div>
<div class="chart-bars"><?php foreach ($days as $date => $day): ?><div class="bar-column"><span class="bar-value"><?= money($day['total']) ?></span><div class="bar-track"><div class="bar <?= $date === date('Y-m-d') ? 'today' : '' ?>" style="height:<?= $day['total'] > 0 ? max(3, $day['total'] / $max * 100) : 0 ?>%" title="<?= esc($day['label'] . ': ' . money($day['total'])) ?>"></div></div><span class="bar-label"><?= $day['label'] ?></span></div><?php endforeach ?></div>
</div>
</section>
<section class="panel stock-panel"><div class="panel-heading"><div><h2>Stock watch</h2><p>A little attention goes a long way</p></div><span class="count-badge"><?= $lowStockCount ?></span></div>
<?php foreach ($lowStock as $product): ?><a class="stock-row" href="<?= site_url('products/' . $product['id'] . '/edit') ?>"><img src="<?= image_url($product['image']) ?>" alt=""><span><strong><?= esc($product['name']) ?></strong><small><?= (int) $product['stock_quantity'] === 0 ? 'Out of stock' : 'Running low' ?></small></span><span class="stock-quantity"><?= esc($product['stock_quantity']) ?><small>left</small></span></a><?php endforeach ?>
<?php if (!$lowStock): ?><div class="empty-state compact"><?= icon('check') ?><h3>Your shelves are ready</h3><p>All active products have healthy stock.</p></div><?php endif ?>
<a class="stock-footer text-link" href="<?= site_url('products') ?>">Manage inventory <?= icon('arrow') ?></a>
</section></div>
<section class="panel"><div class="panel-heading"><div><h2>Recent transactions</h2><p>The latest from your checkout</p></div><a class="text-link" href="<?= site_url('sales') ?>">View all sales <?= icon('arrow') ?></a></div><?= view('partials/sales_table', ['rows' => $recent]) ?></section>
<?= $this->endSection() ?>

