document.addEventListener('DOMContentLoaded', function () {

  // Mobile menu toggle
  var menuToggle = document.getElementById('menuToggle');
  var mainNav = document.getElementById('mainNav');
  if (menuToggle && mainNav) {
    menuToggle.addEventListener('click', function () {
      var isOpen = mainNav.classList.toggle('open');
      menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    document.addEventListener('click', function (event) {
      if (mainNav.classList.contains('open') && !event.target.closest('.site-header')) {
        mainNav.classList.remove('open');
        menuToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // Responsive admin navigation
  var adminShell = document.getElementById('adminShell');
  var adminMenuToggle = document.getElementById('adminMenuToggle');
  if (adminShell && adminMenuToggle) {
    adminMenuToggle.addEventListener('click', function () {
      var isOpen = adminShell.classList.toggle('sidebar-open');
      adminMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    document.addEventListener('click', function (event) {
      if (adminShell.classList.contains('sidebar-open') && !event.target.closest('#adminSidebar') && !event.target.closest('#adminMenuToggle')) {
        adminShell.classList.remove('sidebar-open');
        adminMenuToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;
    if (mainNav) mainNav.classList.remove('open');
    if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
    if (adminShell) adminShell.classList.remove('sidebar-open');
    if (adminMenuToggle) adminMenuToggle.setAttribute('aria-expanded', 'false');
  });

  // Mobile: tap category label to reveal the dropdown menu
  var navDropdown = document.querySelector('.nav-dropdown');
  if (navDropdown) {
    var trigger = navDropdown.querySelector('.nav-dropdown-trigger');
    if (trigger) {
      trigger.addEventListener('click', function () {
        if (window.innerWidth <= 760) {
          navDropdown.classList.toggle('open');
        }
      });
    }
  }

  // Quantity stepper buttons (product page)
  document.querySelectorAll('.qty-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var control = this.closest('.qty-control');
      var input = control ? control.querySelector('input[type="number"]') : null;
      if (!input) return;
      var value = parseInt(input.value, 10) || 1;
      var max = parseInt(input.getAttribute('max'), 10);
      var min = parseInt(input.getAttribute('min'), 10) || 1;
      if (this.dataset.action === 'increase') {
        value = isNaN(max) ? value + 1 : Math.min(max, value + 1);
      } else {
        value = Math.max(min, value - 1);
      }
      input.value = value;
    });
  });

  // Auto-hide flash messages after a few seconds
  document.querySelectorAll('.flash').forEach(function (el) {
    setTimeout(function () {
      el.style.opacity = '0';
      setTimeout(function () { el.style.display = 'none'; }, 400);
    }, 4000);
  });

  // Make the selected checkout payment option visually clear.
  document.querySelectorAll('.radio-option input[type="radio"]').forEach(function (input) {
    function syncPaymentSelection() {
      document.querySelectorAll('.radio-option').forEach(function (label) {
        label.classList.toggle('selected', !!label.querySelector('input:checked'));
      });
    }
    input.addEventListener('change', syncPaymentSelection);
    syncPaymentSelection();
  });

  // Gentle reveal-on-scroll for cards
  var revealTargets = document.querySelectorAll('.product-card, .category-card, .promise-item');
  if ('IntersectionObserver' in window && revealTargets.length) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealTargets.forEach(function (el) { observer.observe(el); });
  } else {
    revealTargets.forEach(function (el) { el.classList.add('is-visible'); });
  }

});
