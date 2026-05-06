document.getElementById('timeBtn')?.addEventListener('click', () => {
    document.getElementById('timeMenu')?.classList.toggle('open');
    document.getElementById('overlay')?.classList.toggle('visible');
});

document.getElementById('allergieBtn')?.addEventListener('click', () => {
    document.getElementById('allergieMenu')?.classList.toggle('open');
    document.getElementById('overlay')?.classList.toggle('visible');
});

document.getElementById('overlay')?.addEventListener('click', () => {
    document.querySelectorAll('.open').forEach(el => el.classList.remove('open'));
    document.getElementById('overlay')?.classList.remove('visible');
});