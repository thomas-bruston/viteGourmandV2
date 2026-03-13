# Vite & Gourmand

Application web de commande de menus pour un restaurant — Projet DWWM Titre Professionnel Niveau 5.

## Stack technique

- **Back-end** : PHP 8.3 (POO, sans framework)
- **Front-end** : HTML5, CSS3, JavaScript ES6+ (vanilla)
- **BDD relationnelle** : MySQL 8.0
- **BDD non relationnelle** : MongoDB 7.0
- **Serveur web** : Nginx
- **Conteneurisation** : Docker + Docker Compose
- **Mails** : PHPMailer 6.x
- **Tests** : PHPUnit 11.x


## Déploiement local (développement)

### Prérequis

- Docker Desktop
- Git

## Installation

**1. Cloner le projet**

git clone https://github.com/thomas-bruston/viteGourmandV2.git
cd viteGourmand


**2. Créer le fichier d'environnement**

cp .env.example .env

**3. Lancer les conteneurs**

docker compose up -d

**4. Installer les dépendances**

docker compose exec app composer install

**5. Créer la base de données**

docker compose exec -T mysql mysql -u root -prootpassword < sql/create.sql
docker compose exec -T mysql mysql -u root -prootpassword --default-character-set=utf8mb4 quai_antique < sql/seed.sql

**6. Ouvrir l'application**

(http://localhost:8080)


### Comptes de test

Administrateur  admin@vitegourmand.fr  Admin@12345 
Employé  employe@vitegourmand.fr  Employe@12345 
Utilisateur  user@vitegourmand.fr  User@12345 

## Lancer les tests

docker compose exec app vendor/bin/phpunit


Projet réalisé dans le cadre du Titre Professionnel DWWM @2026
