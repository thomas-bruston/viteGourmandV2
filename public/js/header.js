(function () {
    'use strict';

    const burgerBtn = document.getElementById('burgerBtn');
    const sideMenu  = document.getElementById('sideMenu');
    const userBtn   = document.getElementById('userBtn');
    const userMenu  = document.getElementById('userMenu');
    const overlay   = document.getElementById('overlay');

    // Burger menu
    if (burgerBtn && sideMenu) {
        burgerBtn.addEventListener('click', () => {
            const isOpen = sideMenu.classList.toggle('open');
            overlay.classList.toggle('visible', isOpen);
            burgerBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            sideMenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        });
    }

    // User menu (absent si visiteur non connecté)
    if (userBtn && userMenu) {
        userBtn.addEventListener('click', () => {
            const isOpen = userMenu.classList.toggle('open');
            overlay.classList.toggle('visible', isOpen);
            userBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            userMenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        });
    }

    // Overlay — ferme tout
    if (overlay) {
        overlay.addEventListener('click', () => {
            if (sideMenu) {
                sideMenu.classList.remove('open');
                sideMenu.setAttribute('aria-hidden', 'true');
            }
            if (userMenu) {
                userMenu.classList.remove('open');
                userMenu.setAttribute('aria-hidden', 'true');
            }
            if (burgerBtn) burgerBtn.setAttribute('aria-expanded', 'false');
            if (userBtn)   userBtn.setAttribute('aria-expanded', 'false');
            overlay.classList.remove('visible');
        });
    }

})();