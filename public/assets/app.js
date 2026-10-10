'use strict';
document.addEventListener('DOMContentLoaded', () => {
  const menu = document.querySelector('.menu-toggle');
  menu?.addEventListener('click', () => {
    const open = document.getElementById('sidebar').classList.toggle('open');
    menu.setAttribute('aria-expanded', String(open));
  });
  document.addEventListener('click', (event) => {
    const sidebar = document.getElementById('sidebar');
    if (sidebar?.classList.contains('open') && !sidebar.contains(event.target) && !menu.contains(event.target)) {
      sidebar.classList.remove('open');
      menu.setAttribute('aria-expanded', 'false');
    }
  });

  const dialog = document.getElementById('confirm-dialog');
  let pendingForm = null;
  document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', event => {
      if (form.dataset.confirmed === 'yes') return;
      event.preventDefault();
      pendingForm = form;
      document.getElementById('confirm-message').textContent = form.dataset.confirm;
      dialog.returnValue = '';
      dialog.showModal();
    });
  });
  dialog?.addEventListener('close', () => {
    if (dialog.returnValue === 'confirm' && pendingForm) {
      pendingForm.dataset.confirmed = 'yes';
      pendingForm.requestSubmit();
    }
    pendingForm = null;
  });

  document.querySelectorAll('[data-save-form]').forEach(form => {
    form.addEventListener('submit', () => {
      if (!form.checkValidity()) return;
      const button = form.querySelector('button[type="submit"]');
      if (button) {
        button.disabled = true;
        button.textContent = 'Saving…';
      }
    });
  });
  // Restore forms when the browser returns a page from its back/forward cache.
  window.addEventListener('pageshow', event => { if (event.persisted) window.location.reload(); });

  const upload = document.querySelector('[data-image-input]');
  let previewURL = null;
  upload?.addEventListener('change', () => {
    const file = upload.files[0];
    if (!file) return;
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
      upload.setCustomValidity('Choose a JPEG, PNG, or WebP image no larger than 2 MB.');
      upload.reportValidity();
      return;
    }
    upload.setCustomValidity('');
    if (previewURL) URL.revokeObjectURL(previewURL);
    previewURL = URL.createObjectURL(file);
    document.getElementById('upload-preview').src = previewURL;
  });

  const saleForm = document.getElementById('sale-form');
  if (!saleForm) return;
  const search = document.getElementById('product-search');
  search.addEventListener('input', () => {
    let visible = 0;
    const query = search.value.trim().toLocaleLowerCase();
    document.querySelectorAll('.sale-product').forEach(card => {
      card.hidden = !card.dataset.productName.includes(query);
      if (!card.hidden) visible++;
    });
    document.getElementById('product-search-empty').hidden = visible !== 0;
  });

  const quantity = document.getElementById('quantity');
  const format = new Intl.NumberFormat('en-PH', {style: 'currency', currency: 'PHP'});
  const update = () => {
    const product = saleForm.querySelector('input[name="product_id"]:checked');
    const units = Number(quantity.value);
    const stock = product ? Number(product.dataset.stock) : 0;
    const valid = Number.isInteger(units) && units > 0 && units <= 1000000 && (!product || units <= stock);
    const cents = product ? Math.round(Number(product.dataset.price) * 100) : 0;
    const tooLarge = cents * units > 9999999999;
    quantity.setCustomValidity(!valid ? (product && units > stock ? 'Only ' + stock + ' unit(s) are available.' : 'Enter a positive whole quantity.') : tooLarge ? 'This transaction total is too large.' : '');
    quantity.max = product ? Math.min(stock, 1000000) : 1000000;
    document.getElementById('quantity-label').textContent = units + (units === 1 ? ' item' : ' items');
    document.getElementById('unit-price').textContent = format.format(cents / 100);
    document.getElementById('sale-total').textContent = format.format(valid && product ? cents * units / 100 : 0);
    document.getElementById('record-sale').disabled = !product || !valid || tooLarge;
    document.getElementById('quantity-feedback').textContent = !valid && product && units > stock ? 'Only ' + stock + ' unit(s) are available. Reduce the quantity.' : tooLarge ? 'Reduce the quantity: total exceeds the transaction limit.' : 'Stock will update when the sale is recorded.';
    if (product) {
      document.getElementById('selected-name').textContent = product.dataset.name;
      document.getElementById('selected-stock').textContent = stock + ' units available';
      document.getElementById('selected-image').src = product.dataset.image;
    }
  };
  saleForm.querySelectorAll('input[name="product_id"]').forEach(input => input.addEventListener('change', update));
  quantity.addEventListener('input', update);
  document.querySelectorAll('[data-quantity]').forEach(button => button.addEventListener('click', () => {
    quantity.value = Math.max(1, Math.min(Number(quantity.max), (Number(quantity.value) || 1) + Number(button.dataset.quantity)));
    update();
  }));
  update();
});

