(function () {
    'use strict';

    const filterButton = document.getElementById('filterButton');
    const filtresMenu  = document.getElementById('filtres-menu');
    const validerBtn   = document.getElementById('valider-btn');
    const resetBtn     = document.getElementById('reset-btn');
    const container    = document.getElementById('cardsContainer');
    const prixMax      = document.getElementById('prix-max');
    const prixLabel    = document.getElementById('prix-max-label');

/* ETAT FILTRES ACTIFS */

    const activeFilters = {
        theme:     null,
        regime:    null,
        personnes: null,
        prix_max:  100
    };

/* OUVRIR + FERMER PANNEAU FILTRES */

 filterButton.addEventListener('click', () => {
    console.log('bouton cliqué');
    const isOpen = filtresMenu.getAttribute('aria-hidden') === 'false';
    console.log('isOpen:', isOpen);
    filtresMenu.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
    filterButton.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
    filtresMenu.classList.toggle('open', !isOpen);
});

/* BTN FILTRE TOGGLE ACIF */

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.dataset.filter;
            const value  = btn.dataset.value;

            
            document.querySelectorAll(`.filter-btn[data-filter="${filter}"]`).forEach(b => {
                b.classList.remove('active');
                b.setAttribute('aria-pressed', 'false');
            });

            if (activeFilters[filter] === value) {
                activeFilters[filter] = null;
                btn.setAttribute('aria-pressed', 'false');
            } else {
                activeFilters[filter] = value;
                btn.classList.add('active');
                btn.setAttribute('aria-pressed', 'true');
            }
        });
    });

/* SLIDER PRIX */

    prixMax.addEventListener('input', () => {
        activeFilters.prix_max = prixMax.value;
        prixLabel.textContent  = prixMax.value;
    });

/* VALIDER + REQUETE */

    validerBtn.addEventListener('click', () => {
        fetchMenus();

/* FERMER PANNEAU */

        filtresMenu.setAttribute('aria-hidden', 'true');
        filterButton.setAttribute('aria-expanded', 'false');
        filtresMenu.classList.remove('open');
    });

/* RENINIT */

    resetBtn.addEventListener('click', () => {
        activeFilters.theme     = null;
        activeFilters.regime    = null;
        activeFilters.personnes = null;
        activeFilters.prix_max  = 100;
        prixMax.value           = 100;
        prixLabel.textContent   = '100';
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('active');
            b.setAttribute('aria-pressed', 'false');
        });
        fetchMenus();
    });

/* REQUETE AJAX */

    function fetchMenus() {
        const params = new URLSearchParams();
        if (activeFilters.theme)     params.append('theme',     activeFilters.theme);
        if (activeFilters.regime)    params.append('regime',    activeFilters.regime);
        if (activeFilters.personnes) params.append('personnes', activeFilters.personnes);
        if (activeFilters.prix_max)  params.append('prix_max',  activeFilters.prix_max);

        container.setAttribute('aria-busy', 'true');
        container.innerHTML = '<p class="loading">Chargement...</p>';

        fetch(`/menus/filtres?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Erreur réseau');
            return response.text();
        })
        .then(html => {
            container.innerHTML = html;
            container.setAttribute('aria-busy', 'false');
        })
        .catch(() => {
            container.innerHTML = '<p class="error" role="alert">Une erreur est survenue. Veuillez réessayer.</p>';
            container.setAttribute('aria-busy', 'false');
        });
    }

})();