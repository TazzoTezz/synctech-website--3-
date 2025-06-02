document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.nav');
  if (toggle && nav) {
    toggle.addEventListener('click', (e) => {
      e.stopPropagation();
      nav.classList.toggle('show');
    });

    // Close nav when clicking outside on mobile
    document.addEventListener('click', (e) => {
      if (nav.classList.contains('show') && !nav.contains(e.target) && !toggle.contains(e.target)) {
        nav.classList.remove('show');
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
