(() => {
  'use strict';

  const $ = (selector, scope = document) => scope.querySelector(selector);
  const $$ = (selector, scope = document) => Array.from(scope.querySelectorAll(selector));
  const demoMode = document.body?.dataset.demoMode === 'true';
  const drawer = $('#cart-drawer');
  const drawerBackdrop = $('[data-cart-backdrop]');
  const cartContent = $('[data-cart-content]');
  const cartFooter = $('[data-cart-footer]');
  const checkoutModal = $('#checkout-modal');
  const searchDialog = $('#search-dialog');
  const toast = $('[data-toast]');
  const storageKeys = { cart: 'tikho-shop-cart', wishlist: 'tikho-shop-wishlist' };
  const shippingThreshold = 8000;
  const formatter = new Intl.NumberFormat('ru-RU', { maximumFractionDigits: 0 });

  const safeRead = (key, fallback) => {
    try {
      const value = window.localStorage.getItem(key);
      return value ? JSON.parse(value) : fallback;
    } catch (error) {
      return fallback;
    }
  };
  const safeWrite = (key, value) => {
    try { window.localStorage.setItem(key, JSON.stringify(value)); } catch (error) { /* Storage may be disabled. */ }
  };
  const money = (value) => `${formatter.format(Number(value) || 0)} ₽`;
  const escapeHTML = (value) => String(value).replace(/[&<>"']/g, (char) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  })[char]);

  let cart = safeRead(storageKeys.cart, {});
  if (!cart || typeof cart !== 'object' || Array.isArray(cart)) cart = {};
  let wishlist = new Set(safeRead(storageKeys.wishlist, []));
  let toastTimer;
  let searchQuery = '';
  let activeFilter = 'all';
  let sortMode = 'featured';

  const productCards = $$('[data-product-card]');
  const productGrid = $('[data-product-grid]');
  const noResults = $('[data-no-results]');
  const searchInput = $('#site-search');

  function showToast(message) {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add('is-visible');
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 2600);
  }

  function syncBodyLock() {
    const drawerOpen = drawer?.classList.contains('is-open');
    const checkoutOpen = checkoutModal && !checkoutModal.hidden;
    const searchOpen = searchDialog?.open;
    document.body.classList.toggle('is-locked', Boolean(drawerOpen || checkoutOpen || searchOpen));
  }

  function openCart() {
    if (!drawer) return;
    drawer.classList.add('is-open');
    drawer.setAttribute('aria-hidden', 'false');
    drawerBackdrop?.classList.add('is-visible');
    syncBodyLock();
    window.setTimeout(() => $('[data-cart-close]', drawer)?.focus(), 80);
  }

  function closeCart() {
    if (!drawer) return;
    drawer.classList.remove('is-open');
    drawer.setAttribute('aria-hidden', 'true');
    drawerBackdrop?.classList.remove('is-visible');
    syncBodyLock();
  }

  function saveCart() {
    safeWrite(storageKeys.cart, cart);
    renderCart();
  }

  function cartItems() { return Object.values(cart); }
  function itemCount() { return cartItems().reduce((sum, item) => sum + Number(item.quantity || 0), 0); }
  function cartTotal() { return cartItems().reduce((sum, item) => sum + Number(item.price || 0) * Number(item.quantity || 0), 0); }

  function renderCart() {
    if (!demoMode || !cartContent) return;
    const items = cartItems();
    const count = itemCount();
    const total = cartTotal();

    $$('.cart-count').forEach((node) => { node.textContent = String(count); });
    const drawerCount = $('[data-drawer-count]');
    if (drawerCount) drawerCount.textContent = `(${count})`;

    if (!items.length) {
      cartContent.innerHTML = `
        <div class="cart-empty">
          <span class="cart-empty-mark" aria-hidden="true">◌</span>
          <h3>Пока здесь тихо.</h3>
          <p>Добавим что-нибудь красивое? Ваши находки будут ждать здесь.</p>
          <button type="button" data-close-cart>Перейти к каталогу ↗</button>
        </div>`;
      if (cartFooter) cartFooter.hidden = true;
      return;
    }

    cartContent.innerHTML = items.map((item) => `
      <article class="cart-line" data-cart-line="${escapeHTML(item.id)}">
        <img class="cart-line-image" src="${escapeHTML(item.image)}" alt="" loading="lazy">
        <div class="cart-line-info">
          <h3>${escapeHTML(item.name)}</h3>
          <p>Ручная работа · тихо</p>
          <div class="quantity-control" aria-label="Количество: ${Number(item.quantity)}">
            <button type="button" aria-label="Уменьшить количество" data-quantity-change="-1" data-item-id="${escapeHTML(item.id)}">−</button>
            <span>${Number(item.quantity)}</span>
            <button type="button" aria-label="Увеличить количество" data-quantity-change="1" data-item-id="${escapeHTML(item.id)}">+</button>
          </div>
        </div>
        <span class="cart-line-price">${money(Number(item.price) * Number(item.quantity))}</span>
      </article>`).join('');

    if (cartFooter) cartFooter.hidden = false;
    const subtotal = $('[data-cart-subtotal]');
    if (subtotal) subtotal.textContent = money(total);
    const progress = $('[data-shipping-progress]');
    if (progress) progress.style.width = `${Math.min(100, (total / shippingThreshold) * 100)}%`;
    const shippingCopy = $('[data-shipping-copy]');
    if (shippingCopy) {
      shippingCopy.textContent = total >= shippingThreshold
        ? 'Ура! Доставка за наш счёт'
        : `До бесплатной доставки осталось ${money(shippingThreshold - total)}`;
    }
  }

  function addDemoProduct(button) {
    const id = button.dataset.productId;
    if (!id) return;
    const item = cart[id] || {
      id,
      name: button.dataset.productName || 'Новая находка',
      price: Number(button.dataset.price) || 0,
      image: button.dataset.image || '',
      quantity: 0
    };
    item.quantity += 1;
    cart[id] = item;
    saveCart();
    openCart();
    showToast(`${item.name} добавлена в корзину`);
  }

  function openSearch() {
    if (!searchDialog) return;
    if (typeof searchDialog.showModal === 'function') searchDialog.showModal();
    else searchDialog.setAttribute('open', '');
    syncBodyLock();
    window.setTimeout(() => searchInput?.focus(), 40);
  }

  function closeSearch() {
    if (!searchDialog) return;
    if (typeof searchDialog.close === 'function' && searchDialog.open) searchDialog.close();
    else searchDialog.removeAttribute('open');
    syncBodyLock();
  }

  function visibleBySearch(card) {
    if (!searchQuery) return true;
    const haystack = `${card.dataset.name || ''} ${card.textContent || ''}`.toLocaleLowerCase('ru-RU');
    return haystack.includes(searchQuery.toLocaleLowerCase('ru-RU'));
  }

  function updateCatalog() {
    if (!productCards.length) return;
    let visible = 0;
    productCards.forEach((card) => {
      const category = (card.dataset.category || '').toLowerCase().split(/\s+/);
      const matchesFilter = activeFilter === 'all' || category.includes(activeFilter);
      const shouldShow = matchesFilter && visibleBySearch(card);
      card.hidden = !shouldShow;
      if (shouldShow) visible += 1;
    });
    if (noResults) noResults.hidden = visible > 0;
    const countLabel = $('[data-filter="all"] span');
    if (countLabel) countLabel.textContent = String(visible).padStart(2, '0');
  }

  function applySearch(value) {
    searchQuery = String(value || '').trim();
    updateCatalog();
    const message = $('[data-search-message]');
    if (message) {
      const shown = productCards.filter((card) => !card.hidden).length;
      message.textContent = searchQuery
        ? (shown ? `Нашлось: ${shown} ${shown === 1 ? 'вещь' : 'вещи'}.` : 'Ничего не нашлось — попробуйте другой запрос.')
        : '';
    }
  }

  function sortCatalog() {
    if (!productGrid) return;
    const cards = [...productCards];
    if (sortMode === 'featured') {
      cards.sort((a, b) => productCards.indexOf(a) - productCards.indexOf(b));
    } else {
      const direction = sortMode === 'price-low' ? 1 : -1;
      cards.sort((a, b) => {
        const priceA = Number(a.querySelector('[data-add-to-cart]')?.dataset.price) || 0;
        const priceB = Number(b.querySelector('[data-add-to-cart]')?.dataset.price) || 0;
        return (priceA - priceB) * direction;
      });
    }
    cards.forEach((card) => productGrid.append(card));
    updateCatalog();
  }

  function openCheckout() {
    if (!checkoutModal) return;
    closeCart();
    checkoutModal.hidden = false;
    window.requestAnimationFrame(() => checkoutModal.classList.add('is-visible'));
    syncBodyLock();
    window.setTimeout(() => $('input', checkoutModal)?.focus(), 80);
  }

  function closeCheckout() {
    if (!checkoutModal) return;
    checkoutModal.classList.remove('is-visible');
    window.setTimeout(() => {
      checkoutModal.hidden = true;
      syncBodyLock();
    }, 260);
  }

  // Cart drawer, product add buttons, and quantity controls.
  $$('[data-cart-open]').forEach((button) => button.addEventListener('click', (event) => {
    event.preventDefault();
    openCart();
  }));
  $$('[data-cart-close]').forEach((button) => button.addEventListener('click', closeCart));
  drawerBackdrop?.addEventListener('click', closeCart);
  $$('[data-add-to-cart]').forEach((button) => button.addEventListener('click', () => {
    if (demoMode) addDemoProduct(button);
  }));
  cartContent?.addEventListener('click', (event) => {
    const closeButton = event.target.closest('[data-close-cart]');
    if (closeButton) {
      closeCart();
      $('#catalog')?.scrollIntoView({ behavior: 'smooth' });
      return;
    }
    const quantityButton = event.target.closest('[data-quantity-change]');
    if (!quantityButton || !demoMode) return;
    const id = quantityButton.dataset.itemId;
    const item = cart[id];
    if (!item) return;
    item.quantity += Number(quantityButton.dataset.quantityChange);
    if (item.quantity <= 0) delete cart[id];
    saveCart();
  });

  // WooCommerce's AJAX add-to-cart event keeps the theme's mini-cart drawer in sync.
  if (!demoMode && window.jQuery) {
    window.jQuery(document.body).on('added_to_cart', () => openCart());
  }

  // Search dialog and on-page catalog search in the standalone preview.
  $$('[data-search-open]').forEach((button) => button.addEventListener('click', openSearch));
  $$('[data-search-close]').forEach((button) => button.addEventListener('click', closeSearch));
  searchDialog?.addEventListener('click', (event) => {
    if (event.target === searchDialog) closeSearch();
  });
  $('[data-search-form]')?.addEventListener('submit', (event) => {
    if (!demoMode) return;
    event.preventDefault();
    applySearch(searchInput?.value || '');
  });
  searchInput?.addEventListener('input', () => {
    if (demoMode && searchInput.value.trim().length > 1) applySearch(searchInput.value);
    else if (demoMode && !searchInput.value.trim()) applySearch('');
  });
  $$('[data-search-suggestion]').forEach((button) => button.addEventListener('click', () => {
    const value = button.dataset.searchSuggestion || '';
    if (searchInput) searchInput.value = value;
    applySearch(value);
    searchInput?.focus();
  }));

  // Category filters and lightweight price sorting.
  $$('[data-filter]').forEach((button) => button.addEventListener('click', () => {
    activeFilter = button.dataset.filter || 'all';
    $$('[data-filter]').forEach((chip) => {
      const isActive = chip === button;
      chip.classList.toggle('is-active', isActive);
      chip.setAttribute('aria-pressed', String(isActive));
    });
    updateCatalog();
  }));
  $('[data-sort-toggle]')?.addEventListener('click', (event) => {
    sortMode = sortMode === 'featured' ? 'price-low' : sortMode === 'price-low' ? 'price-high' : 'featured';
    event.currentTarget.textContent = sortMode === 'featured' ? 'Сначала любимые ⌄' : sortMode === 'price-low' ? 'Сначала дешевле ⌄' : 'Сначала дороже ⌄';
    sortCatalog();
  });

  // Save favorites locally in the standalone preview.
  $$('[data-wishlist]').forEach((button) => {
    const id = button.dataset.wishlist;
    button.setAttribute('aria-pressed', String(wishlist.has(id)));
    button.addEventListener('click', () => {
      const isFavorite = !wishlist.has(id);
      if (isFavorite) wishlist.add(id);
      else wishlist.delete(id);
      safeWrite(storageKeys.wishlist, [...wishlist]);
      button.setAttribute('aria-pressed', String(isFavorite));
      showToast(isFavorite ? 'Добавлено в избранное' : 'Убрано из избранного');
    });
  });

  // Mobile navigation.
  const menuToggle = $('[data-menu-toggle]');
  const mobileNav = $('[data-mobile-nav]');
  menuToggle?.addEventListener('click', () => {
    const isOpen = mobileNav?.classList.toggle('is-open');
    menuToggle.setAttribute('aria-expanded', String(Boolean(isOpen)));
    menuToggle.setAttribute('aria-label', isOpen ? 'Закрыть меню' : 'Открыть меню');
  });
  $$('.primary-nav a').forEach((link) => link.addEventListener('click', () => {
    mobileNav?.classList.remove('is-open');
    menuToggle?.setAttribute('aria-expanded', 'false');
    menuToggle?.setAttribute('aria-label', 'Открыть меню');
  }));

  // Dismissible announcement bar.
  $('[data-dismiss-announcement]')?.addEventListener('click', () => {
    const bar = $('#announcement-bar');
    if (bar) bar.hidden = true;
  });

  // Demo-only newsletter and checkout forms never transmit personal information.
  $('[data-newsletter-form]')?.addEventListener('submit', (event) => {
    event.preventDefault();
    event.currentTarget.reset();
    showToast(demoMode
      ? 'Спасибо! В демо-режиме подписка не отправляется.'
      : 'Для отправки писем подключите форму Contact Form 7.');
  });
  $('[data-checkout-open]')?.addEventListener('click', openCheckout);
  $$('[data-checkout-close]').forEach((button) => button.addEventListener('click', closeCheckout));
  checkoutModal?.addEventListener('click', (event) => {
    if (event.target === checkoutModal) closeCheckout();
  });
  $('[data-checkout-form]')?.addEventListener('submit', (event) => {
    if (!demoMode) return;
    event.preventDefault();
    $('[data-checkout-form-wrap]')?.setAttribute('hidden', '');
    $('[data-checkout-success]')?.removeAttribute('hidden');
  });
  $('[data-checkout-done]')?.addEventListener('click', () => {
    cart = {};
    saveCart();
    $('[data-checkout-success]')?.setAttribute('hidden', '');
    $('[data-checkout-form-wrap]')?.removeAttribute('hidden');
    closeCheckout();
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    if (drawer?.classList.contains('is-open')) closeCart();
    if (checkoutModal && !checkoutModal.hidden) closeCheckout();
    if (searchDialog?.open) closeSearch();
  });

  renderCart();
  updateCatalog();
})();
