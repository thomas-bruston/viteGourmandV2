    const input = document.getElementById('nb_personnes');
    const sousTotalEl = document.getElementById('sous_total');
    const totalEl = document.getElementById('total');

    const prixMenu = parseInt(input.dataset.prix);
    const minPersonnes = parseInt(input.dataset.min);
    const fraisLivraison = 5;

    function updatePrices() {
        let nb = parseInt(input.value) || minPersonnes;

        if (nb < minPersonnes) {
            nb = minPersonnes;
            input.value = minPersonnes;
        }

        const sousTotal = prixMenu * nb;
        const total = sousTotal + fraisLivraison;

        sousTotalEl.textContent = sousTotal.toFixed(2) + ' €';
        totalEl.textContent = total.toFixed(2) + ' €';
    }

    input.addEventListener('input', updatePrices);
    updatePrices()
;

