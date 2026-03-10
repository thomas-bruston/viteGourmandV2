class GestionnaireMenus {
    static instance = null;
    
    constructor() {
        // Si une instance existe, la retourner
        if (GestionnaireMenus.instance) {
            return GestionnaireMenus.instance;
        }
        
        // Stockage des menus gérés
        this.menus = [];
        this.overlay = document.getElementById('overlay');
        
        // Initialiser l'overlay
        this.initialiserOverlay();
        
        // Sauvegarder l'instance unique
        GestionnaireMenus.instance = this;
    }
    
    // Ajouter un menu à gérer
    ajouterMenu(btnId, menuId) {
        const btn = document.getElementById(btnId);
        const menu = document.getElementById(menuId);
        
        if (!btn || !menu) {
            console.error(`Élément introuvable: ${btnId} ou ${menuId}`);
            return;
        }
        
        // Ajouter au tableau des menus
        this.menus.push({ btn, menu });
        
        // Attacher l'événement au bouton
        btn.addEventListener('click', () => this.toggleMenu(menu));
    }
    
    // Initialiser l'overlay pour fermer tous les menus
    initialiserOverlay() {
        this.overlay.addEventListener('click', () => this.fermerTousLesMenus());
    }
    
    // Ouvrir/Fermer un menu spécifique
    toggleMenu(menu) {
        menu.classList.toggle('open');
        this.overlay.classList.toggle('visible');
    }
    
    // Fermer tous les menus
    fermerTousLesMenus() {
        this.menus.forEach(({ menu }) => {
            menu.classList.remove('open');
        });
        this.overlay.classList.remove('visible');
    }
    
    // Méthode statique pour obtenir l'instance
    static getInstance() {
        if (!GestionnaireMenus.instance) {
            GestionnaireMenus.instance = new GestionnaireMenus();
        }
        return GestionnaireMenus.instance;
    }
}

// ============================================
// INITIALISATION
// ============================================

// Créer l'instance unique
const gestionnaire = GestionnaireMenus.getInstance();

// Ajouter les menus à gérer
gestionnaire.ajouterMenu('timeBtn', 'timeMenu');
gestionnaire.ajouterMenu('allergieBtn', 'allergieMenu');
