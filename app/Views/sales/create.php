<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="page-heading"><div><span class="eyebrow">A GREAT CHECKOUT STARTS HERE</span><h1>Record a sale</h1><p>Choose an item, add the details, and make someone’s day.</p></div><a class="button secondary" href="<?= site_url('sales') ?>"><?= icon('receipt') ?> Sales history</a></div>
<form action="<?= site_url('sales') ?>" method="post" class="sale-layout" id="sale-form" data-save-form>
<?= csrf_field() ?><input type="hidden" name="request_key" value="<?= esc($requestKey, 'attr') ?>">
<div class="panel sale-main"><div class="panel-heading"><div class="step-heading"><span>01</span><div><h2>Select a product</h2><p>Find something they’ll love.</p></div></div><span class="pill neutral"><?= count($products) ?> products</span></div>
<div class="sale-search"><?= icon('search') ?><input type="search" id="product-search" aria-label="Filter products" placeholder="Search campus essentials..."></div>
<div class="sale-product-grid" id="sale-products">
<?php $selected = session()->getFlashdata('form_data')['product_id'] ?? ''; foreach ($products as $product): ?>
<label class="sale-product <?= (int) $product['stock_quantity'] === 0 ? 'sold-out' : '' ?>" data-product-name="<?= esc(mb_strtolower($product['name']), 'attr') ?>">
<input type="radio" name="product_id" value="<?= $product['id'] ?>" required <?= (int) $product['stock_quantity'] === 0 ? 'disabled' : '' ?> <?= (string) $selected === (string) $product['id'] ? 'checked' : '' ?> data-name="<?= esc($product['name'], 'attr') ?>" data-price="<?= esc($product['price'], 'attr') ?>" data-stock="<?= esc($product['stock_quantity'], 'attr') ?>" data-image="<?= image_url($product['image']) ?>">
<span class="selection-check"><?= icon('check') ?></span><img src="<?= image_url($product['image']) ?>" alt=""><strong><?= esc($product['name']) ?></strong><span class="sale-product-bottom"><b><?= money($product['price']) ?></b><small><?= (int) $product['stock_quantity'] === 0 ? 'Sold out' : $product['stock_quantity'] . ' left' ?></small></span></label>
<?php endforeach ?>
</div>
<p class="empty-state" id="product-search-empty" hidden>No products match your search.</p>
<?php if (!$products): ?><div class="empty-state"><?= icon('box') ?><h3>Your catalog is empty</h3><p>Add a product before recording your first sale.</p><a class="button primary" href="<?= site_url('products/new') ?>">Add product</a></div><?php endif ?>
</div>
<aside class="panel order-panel"><div class="panel-heading"><div class="step-heading"><span>02</span><div><h2>Sale details</h2><p>One last look before checkout.</p></div></div></div>
<div class="order-body"><div class="selected-product" id="selected-product"><img src="<?= image_url(null) ?>" alt="" id="selected-image"><span><strong id="selected-name">No product selected</strong><small id="selected-stock">Choose an item from the catalog</small></span></div>
<div class="field"><label for="customer_id">Customer <span>optional</span></label><select id="customer_id" name="customer_id"><option value="">Walk-in customer</option><?php $customerSelected = session()->getFlashdata('form_data')['customer_id'] ?? ''; foreach ($customers as $customer): ?><option value="<?= $customer['id'] ?>" <?= (string) $customerSelected === (string) $customer['id'] ? 'selected' : '' ?>><?= esc($customer['full_name']) ?></option><?php endforeach ?></select></div>
<div class="field"><label for="quantity">Quantity</label><div class="quantity-control"><button type="button" data-quantity="-1" aria-label="Decrease quantity"><?= icon('minus') ?></button><input id="quantity" name="quantity" type="number" min="1" max="1000000" step="1" value="<?= form_value('quantity', [], '1') ?>" required><button type="button" data-quantity="1" aria-label="Increase quantity"><?= icon('plus') ?></button></div><small id="quantity-feedback" aria-live="polite">Stock will update when the sale is recorded.</small></div>
<div class="order-divider"></div><div class="order-line"><span>Unit price</span><strong id="unit-price">₱0.00</strong></div><div class="order-line"><span>Quantity</span><span id="quantity-label">1 item</span></div><div class="order-total"><span>Total amount</span><strong id="sale-total" aria-live="polite">₱0.00</strong></div>
<button class="button primary wide" type="submit" id="record-sale"><?= icon('check') ?> Record sale <?= icon('arrow') ?></button><div class="secure-note"><?= icon('lock') ?> Recorded by <?= esc(session('full_name')) ?></div>
</div><div class="order-footnote"><?= icon('leaf') ?> Small purchases. Big campus spirit.</div></aside>
</form>
<?= $this->endSection() ?>

