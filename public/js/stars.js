document.addEventListener('DOMContentLoaded', function () {
    const stars     = document.querySelectorAll('.star');
    const noteInput = document.getElementById('note-value');

    if (!stars.length || !noteInput) return;

    // Initialiser avec la valeur par défaut
    highlightStars(noteInput.value);

    stars.forEach(star => {
        star.addEventListener('click', function () {
            noteInput.value = this.dataset.value;
            highlightStars(this.dataset.value);
        });

        star.addEventListener('mouseover', function () {
            highlightStars(this.dataset.value);
        });

        star.addEventListener('mouseout', function () {
            highlightStars(noteInput.value);
        });
    });

    function highlightStars(value) {
        stars.forEach(star => {
            star.style.color = star.dataset.value <= value ? '#f0c103' : '#ccc';
        });
    }
});