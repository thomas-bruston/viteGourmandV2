(function () {
    'use strict';

    const timeBtn  = document.getElementById('timeBtn');
    const timeMenu = document.getElementById('timeMenu');
    const overlay  = document.getElementById('overlay');

    if (!timeBtn || !timeMenu) return;

    timeBtn.addEventListener('click', () => {
        const isOpen = timeMenu.classList.toggle('open');
        overlay.classList.toggle('visible', isOpen);
        timeBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        timeMenu.setAttribute('aria-hidden',  isOpen ? 'false' : 'true');
    });

    overlay.addEventListener('click', () => {
        timeMenu.classList.remove('open');
        overlay.classList.remove('visible');
        timeBtn.setAttribute('aria-expanded', 'false');
        timeMenu.setAttribute('aria-hidden',  'true');
    });

})();