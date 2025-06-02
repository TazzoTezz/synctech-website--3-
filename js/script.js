document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.nav');

  // Rebuild burger menu logic for reliability
  if (toggle && nav) {
    // Toggle menu on burger click
    toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      nav.classList.toggle('show');
      toggle.classList.toggle('active');
    });

    // Close menu when a nav link is clicked (mobile UX)
    nav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        nav.classList.remove('show');
        toggle.classList.remove('active');
      });
    });

    // Close menu when clicking outside nav or burger
    document.addEventListener('click', (e) => {
      if (
        nav.classList.contains('show') &&
        !nav.contains(e.target) &&
        !toggle.contains(e.target)
      ) {
        nav.classList.remove('show');
        toggle.classList.remove('active');
      }
    });

    // Accessibility: close menu on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && nav.classList.contains('show')) {
        nav.classList.remove('show');
        toggle.classList.remove('active');
      }
    });
  }

  // Fix for Remote Assistance button on Contact page
  const remoteBtn = document.querySelector('.btn-black[onclick*="quickassist"]');
  if (remoteBtn) {
    remoteBtn.addEventListener('click', function (e) {
      e.preventDefault();
      alert(
        'To get support:\n\n1. Press Windows + R\n2. Type quickassist\n3. Press Enter\n4. Follow the on-screen instructions.'
      );
    });
  }
});
