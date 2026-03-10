const ADRESSE_RESTAURANT = "25 rue Turenne, 33000 Bordeaux";
const PRIX_BASE_BORDEAUX = 5.00;
const PRIX_PAR_KM = 0.59;
const CODE_POSTAL_BORDEAUX = "33000";

const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

async function geocoderAdresse(adresse) {
    const response = await fetch(
        `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(adresse)}&format=json&countrycodes=fr&limit=1`,
        { headers: { 'User-Agent': 'RestaurantDelivery/1.0' } }
    );
    if (!response.ok) throw new Error('Erreur lors du géocodage');
    const data = await response.json();
    if (data.length === 0) throw new Error('Adresse introuvable');
    return [data[0].lon, data[0].lat];
}

async function calculerDistance(coordsDepart, coordsArrivee) {
    const response = await fetch(
        `https://router.project-osrm.org/route/v1/driving/${coordsDepart[0]},${coordsDepart[1]};${coordsArrivee[0]},${coordsArrivee[1]}?overview=false`
    );
    if (!response.ok) throw new Error('Erreur lors du calcul de distance');
    const data = await response.json();
    if (data.code !== 'Ok') throw new Error('Impossible de calculer l\'itinéraire');
    return data.routes[0].distance / 1000;
}

function calculerPrixLivraison(distanceKm, codePostal) {
    if (codePostal === CODE_POSTAL_BORDEAUX) return PRIX_BASE_BORDEAUX;
    return PRIX_BASE_BORDEAUX + (distanceKm * PRIX_PAR_KM);
}

function mettreAJourPrix(fraisLivraison) {
    const sousTotalEl = document.getElementById('sous_total');
    if (!sousTotalEl) return;

    const sousTotal = parseFloat(
        sousTotalEl.textContent.replace('€', '').replace(',', '.').trim()
    );
    const total = sousTotal + fraisLivraison;

    const livraisonElement = document.getElementById('frais_livraison');
    if (livraisonElement) {
        livraisonElement.textContent = fraisLivraison.toFixed(2).replace('.', ',') + ' €';
    }

    const totalElement = document.getElementById('total');
    if (totalElement) {
        const strongElement = totalElement.querySelector('strong');
        if (strongElement) strongElement.textContent = total.toFixed(2).replace('.', ',') + ' €';
        else totalElement.innerHTML = '<strong>' + total.toFixed(2).replace('.', ',') + ' €</strong>';
    }
}

function afficherChargement(afficher) {
    const livraisonElement = document.getElementById('frais_livraison');
    if (livraisonElement && afficher) {
        livraisonElement.innerHTML = '<span style="color:#666;">Calcul...</span>';
    }
}

async function calculerFraisLivraison() {
    const adresse    = document.getElementById('adresse_livraison')?.value.trim();
    const codePostal = document.getElementById('code_postal_livraison')?.value.trim();
    const ville      = document.getElementById('ville_livraison')?.value.trim();

    if (!adresse || !codePostal || !ville) return;

    const adresseComplete = `${adresse}, ${codePostal} ${ville}`;

    try {
        afficherChargement(true);
        const coordsRestaurant = await geocoderAdresse(ADRESSE_RESTAURANT);
        await delay(1100);
        const coordsClient = await geocoderAdresse(adresseComplete);
        await delay(200);
        const distance = await calculerDistance(coordsRestaurant, coordsClient);
        const prixLivraison = calculerPrixLivraison(distance, codePostal);

        // Stocker la vraie distance dans le champ caché
        const champDistance = document.getElementById('distance_km');
        if (champDistance) champDistance.value = distance.toFixed(2);
        
        const champPrixLivraison = document.getElementById('prix_livraison_calcule');
        if (champPrixLivraison) champPrixLivraison.value = prixLivraison.toFixed(2);


        mettreAJourPrix(prixLivraison);
    } catch (error) {
        console.error('Erreur calcul livraison:', error);
        mettreAJourPrix(PRIX_BASE_BORDEAUX);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const adresseInput    = document.getElementById('adresse_livraison');
    const codePostalInput = document.getElementById('code_postal_livraison');
    const villeInput      = document.getElementById('ville_livraison');

    if (adresseInput && codePostalInput && villeInput) {
        adresseInput.addEventListener('blur',    calculerFraisLivraison);
        codePostalInput.addEventListener('blur', calculerFraisLivraison);
        villeInput.addEventListener('blur',      calculerFraisLivraison);
    }

    // Bloquer la soumission si distance pas encore calculée
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', async function (e) {
            const codePostal = document.getElementById('code_postal_livraison')?.value.trim();
            const champDistance = document.getElementById('distance_km');
            const distance = champDistance?.value;

            if (codePostal && codePostal !== '33000' && (!distance || distance === '0')) {
                e.preventDefault();
                await calculerFraisLivraison();
                form.submit();
            }
        });
    }
});