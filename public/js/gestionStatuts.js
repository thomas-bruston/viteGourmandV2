(function () {
    'use strict';

    const statusBadges   = document.querySelectorAll('.status-badge');
    const rightCard      = document.querySelector('.right-card');
    const overlay        = document.getElementById('overlay');
    const statusItems    = document.querySelectorAll('.status-item');
    const motifContainer = document.getElementById('motif-container');
    const motifTextarea  = document.getElementById('motif-annulation');
    const btnEnvoyer     = document.getElementById('btn-envoyer-annulation');
    const csrfToken      = document.getElementById('csrf-token-statut');

    let currentCommandeId = null;
    let selectedStatus    = null;

/* OPEN PANNEAU STATUTS */

    statusBadges.forEach(badge => {
        badge.addEventListener('click', function (e) {
            e.stopPropagation();
            const item = this.closest('.user-item');
            currentCommandeId = item?.getAttribute('data-commande-id');
            if (rightCard) rightCard.classList.add('open');
            overlay.classList.add('visible');
            if (motifTextarea) motifTextarea.value = '';
            selectedStatus = null;
        });
    });

/* CLOSE AVEC OVERLAY */

    overlay.addEventListener('click', () => {
        if (rightCard) rightCard.classList.remove('open');
        overlay.classList.remove('visible');
        if (motifContainer) motifContainer.classList.remove('show');
        currentCommandeId = null;
        selectedStatus    = null;
    });

/* CLIC STATUT */

    statusItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            selectedStatus = this.getAttribute('data-status');
            if (selectedStatus === 'annulee') {
                if (motifContainer) motifContainer.classList.add('show');
            } else {
                soumettreStatut(currentCommandeId, selectedStatus, null);
            }
        });
    });

/* ENVOI MOTIF ANNULATION */

    if (btnEnvoyer) {
        btnEnvoyer.addEventListener('click', () => {
            const motif = motifTextarea?.value.trim();

            if (!currentCommandeId) {
                alert('Erreur : aucune commande sélectionnée.');
                return;
            }
            if (!motif) {
                alert('Veuillez indiquer un motif d\'annulation.');
                return;
            }

            soumettreStatut(currentCommandeId, 'annulee', motif);
        });
    }

/* ENVOI NOUVEAU STATUT */

    function soumettreStatut(commandeId, statut, motif) {
        const formData = new FormData();
        formData.append('commande_id', commandeId);
        formData.append('statut',      statut);
        formData.append('csrf_token',  csrfToken?.value ?? '');

        let url = '/employe/commande/statut';

        if (statut === 'annulee') {
            url = '/employe/commande/annuler';
            formData.append('motif_annulation', motif);
        }

        fetch(url, {
            method:  'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body:    formData,
        })
        .then(response => {
            if (!response.ok) throw new Error('Erreur réseau');
            window.location.reload();
        })
        .catch(() => {
            alert('Une erreur est survenue. Veuillez réessayer.');
        });
    }

})();