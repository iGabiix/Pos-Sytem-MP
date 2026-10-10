<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="page-heading"><div><span class="eyebrow">EVERY TRANSACTION, ACCOUNTED FOR</span><h1>Sales history</h1><p>A clear view of every purchase at your campus store.</p></div><a class="button primary" href="<?= site_url('sales/new') ?>"><?= icon('plus') ?> Record a sale</a></div>
<div class="history-stats"><div><span>Total revenue</span><strong><?= money($summary['revenue']) ?></strong></div><div><span>Transactions</span><strong><?= number_format($summary['transactions']) ?></strong></div><div><span>Items sold</span><strong><?= number_format($summary['units']) ?></strong></div><div class="history-note"><?= icon('check') ?><span>Completed sales<br><small>All time</small></span></div></div>
<div class="list-toolbar"><form class="search-form" action="<?= site_url('sales') ?>" method="get"><?= icon('search') ?><input type="search" name="q" aria-label="Search transactions" value="<?= esc($q, 'attr') ?>" placeholder="Search product, customer, or staff..."><button class="button small secondary">Search</button></form><?php if ($q !== ''): ?><a class="text-link" href="<?= site_url('sales') ?>">Clear search</a><?php endif ?><span class="muted">Most recent first</span></div>
<section class="panel"><?= view('partials/sales_table', ['rows' => $rows]) ?></section><div class="pagination-wrap"><?= $pager->links() ?></div>
<?= $this->endSection() ?>

