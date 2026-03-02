-- VITE & GOURMAND — Données de test (seed)

USE `viteGourmand`;
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;


-- ROLES

INSERT INTO `role` (`role_id`, `libelle`) VALUES
(1, 'utilisateur'),
(2, 'employe'),
(3, 'administrateur');


-- UTILISATEURS
-- Admin@12345 / Employe@12345 / User@12345

INSERT INTO `utilisateur`
    (`utilisateur_id`, `role_id`, `nom`, `prenom`, `email`, `mot_de_passe`, `telephone`, `adresse`, `code_postal`, `ville`, `statut`)
VALUES
-- Administrateur (compte créé directement en BDD, pas depuis l'appli)
(1, 3, 'Dussol', 'José', 'admin@vitegourmand.fr',
 '$2y$10$TL47d4nBEmUOQjGoTDBecOEoTjPuOleOKD8HK5L/wSZ.LC9nCsPSK',
 '06 26 22 22 61', '132 rue des Violettes', '33000', 'Bordeaux', 1),

-- Employés
(2, 2, 'Rezoul', 'Maxime', 'employe@vitegourmand.fr',
 '$2y$10$stwmoVl1MAqoCIR99U9.3OX3IvVjn.J0pBgaOuL.UbRo2UhKt1fjm',
 '06 12 35 47 65', '12 rue Pasteur', '33000', 'Bordeaux', 1),

(3, 2, 'Delors', 'Sylvain', 'sdelors@vitegourmand.fr',
 '$2y$10$VT/RXmp4e0OgeAyA4O1rCeW/4QkRM4HX9KV0vn.75h0bWyaCxZWBa',
 '06 77 55 88 99', '13 rue des Papillons', '33000', 'Bordeaux', 1),

-- Utilisateurs clients
(4, 1, 'Dubois', 'Laurent', 'user@vitegourmand.fr',
 '$2y$10$61GU25vtTjhXYyJMwcKIJezWQh84lUK9m/V5ZKFAkWgjp25rgxROS',
 '07 55 44 33 23', '6 rue Lucie Aubrac', '33000', 'Bordeaux', 1),

(5, 1, 'Belkari', 'Amina', 'amina.belkari@hotmail.fr',
 '$2y$10$6L.1lBI6AiQ1koI7SY/Ma.BtxHNx3GDNO8hAUdb82SP/Woodem60S',
 '06 77 88 99 21', '10 rue de la Paix', '33000', 'Bordeaux', 1),

(6, 1, 'Belfort', 'Martin', 'martin.belfort@proton.me',
 '$2y$10$izH9RKWerlf4Zi8dBIyPNe4o8g6O/1LMkWijF63uZ3p7hkYkec.Fy',
 '06 77 12 14 43', '35 rue de Bibonne', '33370', 'Tresses', 1);


-- THEMES

INSERT INTO `theme` (`theme_id`, `libelle`) VALUES
(1, 'Gastronomie'),
(2, 'Bistronomie'),
(3, 'Cuisine du monde'),
(4, 'Végétarien'),
(5, 'Événements');


-- REGIMES

INSERT INTO `regime` (`regime_id`, `libelle`) VALUES
(1, 'Classique'),
(2, 'Végétarien'),
(3, 'Sans porc');


-- ALLERGENES

INSERT INTO `allergene` (`allergene_id`, `libelle`) VALUES
(1, 'Gluten'),
(2, 'Œuf'),
(3, 'Lactose'),
(4, 'Arachides'),
(5, 'Crustacés'),
(6, 'Moutarde'),
(7, 'Soja');


-- CATEGORIES DE PLATS

INSERT INTO `categorie_plat` (`categorie_id`, `libelle`, `ordre`) VALUES
(1, 'Entrée',  1),
(2, 'Plat',    2),
(3, 'Dessert', 3);


-- MENUS

INSERT INTO `menu`
    (`menu_id`, `titre`, `description`, `nombre_personne_minimum`, `prix_par_personne`, `image`)
VALUES
(1, 'Le Réception',   'Un menu parfait pour accompagner vos réceptions et événements.',           2, 36.00, 'images/menus/reception.png'),
(2, 'Le Bistro',      'Un menu simple à déguster entre amis ou en famille.',                      2, 25.00, 'images/menus/bistro.png'),
(3, 'Le Dinatoire',   'Un apéritif dinatoire complet et varié pour toutes les envies.',           6, 20.00, 'images/menus/dinatoire.png'),
(4, 'L\'Athènes',     'Voyagez aux confins de la cuisine grecque.',                               2, 25.00, 'images/menus/athenes.png'),
(5, 'Le Marrakech',   'Un menu traditionnel à partager, qui respecte le régime halal.',           4, 28.00, 'images/menus/marrakech.png'),
(6, 'Le Barcelone',   'Découvrez la cuisine des tapaserías catalanes.',                           2, 25.00, 'images/menus/barcelona.png'),
(7, 'Le Roma',        'Tous les plats de ce menu mènent à Rome.',                                 2, 25.00, 'images/menus/roma.png'),
(8, 'Le Champêtre',   'Un menu élaboré avec des plats inédits dans la cuisine végétarienne.',     2, 26.00, 'images/menus/champetre.png'),
(9, 'Le St Valentin', 'Le menu des amoureux à partager sans modération.',                         2, 30.00, 'images/menus/valentin.png');


-- MENU THEME

INSERT INTO `menu_theme` (`menu_id`, `theme_id`) VALUES
(1, 1), -- Réception → Gastronomie
(2, 2), -- Bistro → Bistronomie
(3, 2), -- Dinatoire → Bistronomie
(4, 3), -- Athènes → Cuisine du monde
(5, 3), -- Marrakech → Cuisine du monde
(6, 3), -- Barcelone → Cuisine du monde
(7, 3), -- Roma → Cuisine du monde
(8, 4), -- Champêtre → Végétarien
(9, 5); -- St Valentin → Événements


-- MENU ↔ REGIME

INSERT INTO `menu_regime` (`menu_id`, `regime_id`) VALUES
(1, 1), -- Réception → Classique
(2, 1), -- Bistro → Classique
(3, 1), -- Dinatoire → Classique
(4, 1), -- Athènes → Classique
(5, 3), -- Marrakech → Sans porc
(6, 1), -- Barcelone → Classique
(7, 2), -- Roma → Végétarien
(8, 2), -- Champêtre → Végétarien
(9, 1); -- St Valentin → Classique


-- PLATS

INSERT INTO `plat` (`plat_id`, `menu_id`, `categorie_id`, `nom`, `image`) VALUES
-- Menu 1 — Le Réception
(1,  1, 1, 'Sashimi de saumon, betteraves et fleurs de sureau',   'images/plates/entree gastro.png'),
(2,  1, 2, 'Fondant de bœuf et son écrasé de panais',             'images/plates/plat gastro.png'),
(3,  1, 3, 'Entremet pistache et sa crème vanille',               'images/plates/dessert gastro.png'),
-- Menu 2 — Le Bistro
(4,  2, 1, 'La véritable salade niçoise : thon, œuf et olives noires', 'images/plates/entree bistro.png'),
(5,  2, 2, 'Sauté de veau et ses pommes de terre grenailles',     'images/plates/plat bistro.png'),
(6,  2, 3, 'Soupe de fraise et son moelleux au citron',           'images/plates/dessert bistro.png'),
-- Menu 3 — Le Dinatoire
(7,  3, 1, 'Toast avocat crevettes et ses tomates cerises',       'images/plates/entree dinatoire.png'),
(8,  3, 2, 'Plateau de charcuterie, fromages et légumes croquants', 'images/plates/plat dinatoire.png'),
(9,  3, 3, 'Assortiment de fruits exotiques et tropicaux',        'images/plates/dessert dinatoire.png'),
-- Menu 4 — L'Athènes
(10, 4, 1, 'Salade grecque, tomates anciennes, feta et olives noires', 'images/plates/entree athenes.png'),
(11, 4, 2, 'Moussaka aubergines et bœuf, sauce béchamel',         'images/plates/plat athenes.png'),
(12, 4, 3, 'Mousse de cerises sur moelleux aux amandes',          'images/plates/dessert athenes.png'),
-- Menu 5 — Le Marrakech
(13, 5, 1, 'Salade marocaine tomates, poivrons, menthe et pois chiches', 'images/plates/entree marrakech.png'),
(14, 5, 2, 'Tajine de poulet aux olives vertes et citrons confits', 'images/plates/plat marrakech.png'),
(15, 5, 3, 'Assortiment de pâtisseries orientales faites par nos soins', 'images/plates/dessert marrakech.png'),
-- Menu 6 — Le Barcelone
(16, 6, 1, 'Assortiment de tapas catalans, typiques de Barcelone', 'images/plates/entree barcelona.png'),
(17, 6, 2, 'Empanadas au poulet, thon et bœuf et leur sauce chimichuri', 'images/plates/plat barcelona.png'),
(18, 6, 3, 'Tujon maison aux amandes et noix torréfiées',         'images/plates/dessert barcelona.png'),
-- Menu 7 — Le Roma
(19, 7, 1, 'Mozzarella di bufala et ses tomates cerises',         'images/plates/entree roma.png'),
(20, 7, 2, 'Risotto d\'asperges blanches au safran et parmesan AOC', 'images/plates/plat roma.png'),
(21, 7, 3, 'Véritable tiramisu au mascarpone AOC et son café romain', 'images/plates/dessert roma.png'),
-- Menu 8 — Le Champêtre
(22, 8, 1, 'Salade de roquette et ses légumes croustillants',     'images/plates/entree vegan.png'),
(23, 8, 2, 'Croustillants de légumes et falafels assortis',       'images/plates/plat vegan.png'),
(24, 8, 3, 'Entremet pistache et sa crème vanille',               'images/plates/dessert vegan.png'),
-- Menu 9 — Le St Valentin
(25, 9, 1, 'Asperges rôties au bacon, sauce hollandaise',         'images/plates/entree valentin.png'),
(26, 9, 2, 'Filet mignon et ses légumes rôtis en sauce forestière', 'images/plates/plat valentin.png'),
(27, 9, 3, 'Cœur moelleux citron yuzu et fraise des bois',        'images/plates/dessert valentin.png');


-- PLAT ↔ ALLERGENE

INSERT INTO `plat_allergene` (`plat_id`, `allergene_id`) VALUES
-- Réception
(1, 5), (2, 1), (2, 3), (3, 1), (3, 3),
-- Bistro
(4, 2), (4, 1), (5, 1), (6, 1), (6, 2),
-- Dinatoire
(7, 5), (8, 1), (8, 3),
-- Athènes
(10, 3), (11, 1), (11, 3), (12, 4), (12, 1),
-- Marrakech
(13, 4), (14, 1), (14, 3),
-- Barcelone
(16, 3), (16, 1), (17, 1), (18, 4),
-- Roma
(19, 3), (20, 3), (21, 3), (21, 2),
-- Champêtre
(22, 1), (23, 1), (23, 3), (24, 1), (24, 3), (24, 6),
-- St Valentin
(25, 2), (25, 3), (26, 1), (27, 1), (27, 3), (27, 2);


-- COMMANDES


-- COMMANDES

INSERT INTO `commande`
    (`commande_id`, `utilisateur_id`, `menu_id`, `numero_commande`, `date_commande`, `date_prestation`, `heure_livraison`, `adresse_livraison`, `code_postal_livraison`, `ville_livraison`, `nombre_personnes`, `prix_menu`, `prix_livraison`, `prix_total`, `statut`)
VALUES
(20, 4, 2,  'VG-20260008', '2026-03-19 22:12:10', '2026-03-26', '15:14:00', '780 rue des bouisses', '34070', 'Montpellier', 6,  150.00, 10.00,   160.00,  'terminee'),
(21, 4, 9,  'VG-20260009', '2026-03-19 22:26:59', '2026-03-31', '12:08:00', '780 rue des bouisses', '34070', 'Montpellier', 4,  120.00, 10.00,   130.00,  'terminee'),
(22, 4, 5,  'VG-20260010', '2026-03-19 22:30:15', '2026-03-31', '12:35:00', '780 rue des bouisses', '34070', 'Montpellier', 8,  224.00, 295.82,  519.82,  'terminee'),
(19, 4, 1,  'VG-20260007', '2026-03-19 15:46:36', '2026-03-26', '13:45:00', '780 rue des bouisses', '34070', 'Montpellier', 2,  72.00,  10.00,   82.00,   'acceptee'),
(18, 4, 4,  'VG-20260006', '2026-03-19 15:21:41', '2026-03-27', '10:16:00', '780 rue des bouisses', '34070', 'Montpellier', 2,  50.00,  10.00,   60.00,   'en_attente');

-- SUIVI COMMANDES
INSERT INTO `suivi_commande` (`suivi_id`, `commande_id`, `statut`, `commentaire`, `date_modification`) VALUES
(38, 18, 'en_attente', 'Commande reçue',  '2026-03-19 15:21:41'),
(39, 19, 'en_attente', 'Commande reçue',  '2026-03-19 15:46:36'),
(40, 20, 'en_attente', 'Commande reçue',  '2026-03-19 22:12:10'),
(41, 21, 'en_attente', 'Commande reçue',  '2026-03-19 22:26:59'),
(42, 22, 'en_attente', 'Commande reçue',  '2026-03-19 22:30:15'),
(43, 19, 'acceptee',   NULL,              '2026-03-20 12:34:55'),
(44, 22, 'terminee',   NULL,              '2026-03-20 12:35:01'),
(45, 21, 'terminee',   NULL,              '2026-03-20 12:35:04'),
(46, 20, 'terminee',   NULL,              '2026-03-20 12:35:07');

-- CONTACT

INSERT INTO `contact` (`nom`, `prenom`, `email`, `titre`, `message`) VALUES
('Laurent',  'Martin', 'martin.laurent@gmail.com', 'Problème de connexion',        'Bonjour, j\'ai un problème de connexion à mon compte.'),
('Bernard',  'Paul',   'paul.bernard@hotmail.fr',  'Question paiement',            'Bonjour, peut-on payer par carte bancaire ?'),
('Fontaine', 'Claire', 'claire.f@gmail.com',       'Commande pour 50 personnes',   'Bonjour, est-il possible de commander pour un événement de 50 personnes ?');


-- HORAIRES

INSERT INTO `horaire` (`horaire_id`, `texte`) VALUES
(1, 'Du lundi au dimanche : 9h00 - 20h00');


-- AVIS

INSERT INTO `avis` (`avis_id`, `utilisateur_id`, `commande_id`, `note`, `commentaire`, `statut`, `date_avis`) VALUES
(13, 4, NULL, 5, 'Super moment merci !', 'valide', '2026-03-23 08:29:47');

SET FOREIGN_KEY_CHECKS = 1;
