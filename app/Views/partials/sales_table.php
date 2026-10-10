<div class="table-scroll"><table><thead><tr><th>Transaction / Product</th><th>Customer</th><th>Staff</th><th class="right">Qty</th><th class="right">Total</th><th>Date</th></tr></thead><tbody>
<?php foreach ($rows as $sale): ?><tr>
<td><div class="table-product"><img src="<?= image_url($sale['product_image']) ?>" alt=""><span><strong><?= esc($sale['product_name']) ?></strong><small>#<?= str_pad((string) $sale['id'], 5, '0', STR_PAD_LEFT) ?></small></span></div></td>
<td><?= esc($sale['customer_name'] ?? 'Walk-in customer') ?></td><td><span class="staff-tag"><?= esc(initials($sale['staff_name'])) ?></span><?= esc($sale['staff_name']) ?></td>
<td class="right tabular"><?= esc($sale['quantity']) ?></td><td class="right amount"><?= money($sale['total_price']) ?></td><td class="date-cell"><?= date('M d, Y', strtotime($sale['created_at'])) ?><small><?= date('g:i A', strtotime($sale['created_at'])) ?></small></td>
</tr><?php endforeach ?>
<?php if (!$rows): ?><tr><td colspan="6"><div class="empty-state"><?= icon('receipt') ?><h3>No transactions yet</h3><p>Your recorded sales will appear here.</p><a class="text-link" href="<?= site_url('sales/new') ?>">Record your first sale <?= icon('arrow') ?></a></div></td></tr><?php endif ?>
</tbody></table></div>

