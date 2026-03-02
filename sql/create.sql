-- VITE & GOURMAND — Script de création de la base de données

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;


CREATE DATABASE IF NOT EXISTS `viteGourmand`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `viteGourmand`;


-- TABLE : role

CREATE TABLE `role` (
    `role_id`  INT          NOT NULL AUTO_INCREMENT,
    `libelle`  VARCHAR(50)  NOT NULL,
    PRIMARY KEY (`role_id`),
    UNIQUE KEY `uq_role_libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : utilisateur

CREATE TABLE `utilisateur` (
    `utilisateur_id`  INT           NOT NULL AUTO_INCREMENT,
    `role_id`         INT           NOT NULL DEFAULT 1,
    `nom`             VARCHAR(100)  NOT NULL,
    `prenom`          VARCHAR(100)  NOT NULL,
    `email`           VARCHAR(255)  NOT NULL,
    `mot_de_passe`    VARCHAR(255)  NOT NULL,
    `telephone`       VARCHAR(20)   NOT NULL,
    `adresse`         VARCHAR(255)  NOT NULL,
    `code_postal`     VARCHAR(10)   NOT NULL,
    `ville`           VARCHAR(100)  NOT NULL,
    `statut`          TINYINT(1)    NOT NULL DEFAULT 1 COMMENT '1=actif, 0=désactivé',
    `date_creation`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`utilisateur_id`),
    UNIQUE KEY `uq_utilisateur_email` (`email`),
    KEY `fk_utilisateur_role` (`role_id`),
    CONSTRAINT `fk_utilisateur_role`
        FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`)
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : password_reset_token

CREATE TABLE `password_reset_token` (
    `token_id`        INT           NOT NULL AUTO_INCREMENT,
    `utilisateur_id`  INT           NOT NULL,
    `token`           VARCHAR(64)   NOT NULL,
    `expire_at`       DATETIME      NOT NULL,
    `utilise`         TINYINT(1)    NOT NULL DEFAULT 0,
    PRIMARY KEY (`token_id`),
    UNIQUE KEY `uq_token` (`token`),
    KEY `fk_token_utilisateur` (`utilisateur_id`),
    CONSTRAINT `fk_token_utilisateur`
        FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : theme

CREATE TABLE `theme` (
    `theme_id`  INT          NOT NULL AUTO_INCREMENT,
    `libelle`   VARCHAR(100) NOT NULL,
    PRIMARY KEY (`theme_id`),
    UNIQUE KEY `uq_theme_libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : regime

CREATE TABLE `regime` (
    `regime_id`  INT          NOT NULL AUTO_INCREMENT,
    `libelle`    VARCHAR(100) NOT NULL,
    PRIMARY KEY (`regime_id`),
    UNIQUE KEY `uq_regime_libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : allergene

CREATE TABLE `allergene` (
    `allergene_id`  INT          NOT NULL AUTO_INCREMENT,
    `libelle`       VARCHAR(100) NOT NULL,
    PRIMARY KEY (`allergene_id`),
    UNIQUE KEY `uq_allergene_libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : categorie_plat

CREATE TABLE `categorie_plat` (
    `categorie_id`  INT          NOT NULL AUTO_INCREMENT,
    `libelle`       VARCHAR(50)  NOT NULL,
    `ordre`         INT          NOT NULL DEFAULT 1,
    PRIMARY KEY (`categorie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : menu

CREATE TABLE `menu` (
    `menu_id`                   INT           NOT NULL AUTO_INCREMENT,
    `titre`                     VARCHAR(255)  NOT NULL,
    `description`               TEXT          NOT NULL,
    `nombre_personne_minimum`   INT           NOT NULL DEFAULT 2,
    `prix_par_personne`         DECIMAL(8,2)  NOT NULL,
    `quantite_restante`         INT           NOT NULL DEFAULT 100,
    `image`                     VARCHAR(255)  NOT NULL DEFAULT '',
    `actif`                     TINYINT(1)    NOT NULL DEFAULT 1,
    PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : menu_theme

CREATE TABLE `menu_theme` (
    `menu_id`   INT NOT NULL,
    `theme_id`  INT NOT NULL,
    PRIMARY KEY (`menu_id`, `theme_id`),
    CONSTRAINT `fk_mt_menu`
        FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_mt_theme`
        FOREIGN KEY (`theme_id`) REFERENCES `theme` (`theme_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : menu_regime 

CREATE TABLE `menu_regime` (
    `menu_id`    INT NOT NULL,
    `regime_id`  INT NOT NULL,
    PRIMARY KEY (`menu_id`, `regime_id`),
    CONSTRAINT `fk_mr_menu`
        FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_mr_regime`
        FOREIGN KEY (`regime_id`) REFERENCES `regime` (`regime_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : plat

CREATE TABLE `plat` (
    `plat_id`       INT           NOT NULL AUTO_INCREMENT,
    `menu_id`       INT           NOT NULL,
    `categorie_id`  INT           NOT NULL,
    `nom`           VARCHAR(255)  NOT NULL,
    `image`         VARCHAR(255)  NOT NULL DEFAULT '',
    PRIMARY KEY (`plat_id`),
    KEY `fk_plat_menu` (`menu_id`),
    KEY `fk_plat_categorie` (`categorie_id`),
    CONSTRAINT `fk_plat_menu`
        FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_plat_categorie`
        FOREIGN KEY (`categorie_id`) REFERENCES `categorie_plat` (`categorie_id`)
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : plat_allergene 

CREATE TABLE `plat_allergene` (
    `plat_id`      INT NOT NULL,
    `allergene_id` INT NOT NULL,
    PRIMARY KEY (`plat_id`, `allergene_id`),
    CONSTRAINT `fk_pa_plat`
        FOREIGN KEY (`plat_id`) REFERENCES `plat` (`plat_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_pa_allergene`
        FOREIGN KEY (`allergene_id`) REFERENCES `allergene` (`allergene_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : commande

CREATE TABLE `commande` (
    `commande_id`             INT           NOT NULL AUTO_INCREMENT,
    `utilisateur_id`          INT           NOT NULL,
    `menu_id`                 INT           NOT NULL,
    `numero_commande`         VARCHAR(20)   NOT NULL COMMENT 'Ex: VG-20240001',
    `date_commande`           DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_prestation`         DATE          NOT NULL,
    `heure_livraison`         TIME          NOT NULL,
    `adresse_livraison`       VARCHAR(255)  NOT NULL,
    `code_postal_livraison`   VARCHAR(10)   NOT NULL,
    `ville_livraison`         VARCHAR(100)  NOT NULL,
    `nombre_personnes`        INT           NOT NULL,
    `prix_menu`               DECIMAL(8,2)  NOT NULL,
    `prix_livraison`          DECIMAL(8,2)  NOT NULL DEFAULT 5.00,
    `prix_total`              DECIMAL(8,2)  NOT NULL,
    `statut`                  ENUM(
                                'en_attente',
                                'acceptee',
                                'en_preparation',
                                'en_cours_livraison',
                                'livree',
                                'en_attente_retour_materiel',
                                'terminee',
                                'annulee'
                              ) NOT NULL DEFAULT 'en_attente',
    `motif_annulation`            TEXT          NULL,
    `pret_materiel`               TINYINT(1)    NOT NULL DEFAULT 0,
    `validation_materiel`         TINYINT(1)    NOT NULL DEFAULT 0,
    PRIMARY KEY (`commande_id`),
    UNIQUE KEY `uq_numero_commande` (`numero_commande`),
    KEY `fk_commande_utilisateur` (`utilisateur_id`),
    KEY `fk_commande_menu` (`menu_id`),
    CONSTRAINT `fk_commande_utilisateur`
        FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`)
        ON UPDATE CASCADE,
    CONSTRAINT `fk_commande_menu`
        FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`)
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : suivi_commande

CREATE TABLE `suivi_commande` (
    `suivi_id`    INT          NOT NULL AUTO_INCREMENT,
    `commande_id` INT          NOT NULL,
    `statut`      VARCHAR(50)  NOT NULL,
    `commentaire` TEXT         NULL,
    `date_modification` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`suivi_id`),
    KEY `fk_suivi_commande` (`commande_id`),
    CONSTRAINT `fk_suivi_commande`
        FOREIGN KEY (`commande_id`) REFERENCES `commande` (`commande_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : avis

CREATE TABLE `avis` (
    `avis_id`        INT       NOT NULL AUTO_INCREMENT,
    `utilisateur_id` INT       NOT NULL,
    `commande_id`    INT       NULL COMMENT 'Optionnel — avis sans commande possible',
    `note`           TINYINT   NOT NULL DEFAULT 5 COMMENT 'Note de 1 à 5',
    `commentaire`    TEXT      NOT NULL,
    `statut`         ENUM('en_attente', 'valide', 'refuse') NOT NULL DEFAULT 'en_attente',
    `date_avis`      DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`avis_id`),
    KEY `fk_avis_utilisateur` (`utilisateur_id`),
    CONSTRAINT `fk_avis_utilisateur`
        FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `chk_avis_note` CHECK (`note` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : contact

CREATE TABLE `contact` (
    `contact_id`  INT           NOT NULL AUTO_INCREMENT,
    `nom`         VARCHAR(100)  NOT NULL,
    `prenom`      VARCHAR(100)  NOT NULL,
    `email`       VARCHAR(255)  NOT NULL,
    `titre`       VARCHAR(255)  NOT NULL,
    `message`     TEXT          NOT NULL,
    `date_envoi`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `lu`          TINYINT(1)    NOT NULL DEFAULT 0,
    PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE : horaire

CREATE TABLE `horaire` (
    `horaire_id`  INT   NOT NULL AUTO_INCREMENT,
    `texte`       TEXT  NOT NULL,
    PRIMARY KEY (`horaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;


-- UTILISATEURS 

CREATE USER IF NOT EXISTS 'vg_app'@'%' IDENTIFIED BY 'VgApp@2024!';
GRANT SELECT, INSERT, UPDATE, DELETE ON `viteGourmand`.* TO 'vg_app'@'%';


CREATE USER IF NOT EXISTS 'vg_readonly'@'%' IDENTIFIED BY 'VgRead@2024!';
GRANT SELECT ON `viteGourmand`.* TO 'vg_readonly'@'%';

FLUSH PRIVILEGES;
