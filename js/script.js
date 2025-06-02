document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.nav');
  const body = document.body;

  // --- Burger Menu Logic ---
  if (toggle && nav) {
    // Toggle menu on burger click
    toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      const isOpen = nav.classList.toggle('show');
      toggle.classList.toggle('active', isOpen);
      body.classList.toggle('nav-open', isOpen);
    });

    // Close menu when a nav link is clicked (mobile UX)
    nav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        nav.classList.remove('show');
        toggle.classList.remove('active');
        body.classList.remove('nav-open');
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
        body.classList.remove('nav-open');
      }
    });

    // Accessibility: close menu on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && nav.classList.contains('show')) {
        nav.classList.remove('show');
        toggle.classList.remove('active');
        body.classList.remove('nav-open');
      }
    });
  }

  // --- Logo always links to index ---
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

  // --- Remote Assistance button fix ---
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
