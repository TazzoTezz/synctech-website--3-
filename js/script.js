document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.nav');

  // Robust burger menu logic
  if (toggle && nav) {
    // Toggle menu on burger click
    toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      const isOpen = nav.classList.toggle('show');
      toggle.classList.toggle('active', isOpen);
      // Prevent body scroll when menu is open (mobile UX)
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Close menu when a nav link is clicked (mobile UX)
    nav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        nav.classList.remove('show');
        toggle.classList.remove('active');
        document.body.style.overflow = '';
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
        document.body.style.overflow = '';
      }
    });

    // Accessibility: close menu on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && nav.classList.contains('show')) {
        nav.classList.remove('show');
        toggle.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  }

  // Make logo always link to index (for all pages)
  const logo = document.querySelector('.logo img');
  if (logo) {
    const parent = logo.parentElement;
    if (!parent || parent.tagName.toLowerCase() !== 'a') {
      const link = document.createElement('a');
      link.href = 'index';
      logo.parentNode.insertBefore(link, logo);
      link.appendChild(logo);
    } else {
      parent.href = 'index';
    }
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
