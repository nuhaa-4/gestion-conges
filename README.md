# 📅 Application de Gestion des Demandes de Congés

[![Laravel 11](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)](https://www.php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)
[![Status: Completed](https://img.shields.io/badge/Status-Completed-success?style=for-the-badge)](#)

Une application web moderne, robuste et sécurisée de gestion et de validation des demandes de congés pour les collaborateurs d'une entreprise. Ce projet a été développé dans le cadre d'un stage par **Nouha Kharbouch**.

---

## 📝 À propos du Projet

Cette application vise à **digitaliser et simplifier le circuit de demande et de validation des congés** au sein d'une organisation. Elle permet de remplacer les processus manuels sur papier par un intranet fluide.

💡 **Adaptation au Code du Travail Marocain** :
Contrairement aux applications classiques calquées sur le modèle français (avec RTT), cette plateforme intègre les spécificités du droit du travail marocain :
*   **Congé de Récupération** : Remplacement des RTT par des jours de récupération pour compenser le travail effectué pendant les week-ends ou jours fériés (heures supplémentaires).
*   **Durées légales imposées** : Gestion automatique des congés maternité (14 semaines / 98 jours), paternité (3 jours), événements familiaux (mariages, décès, circoncision) avec calcul automatique et verrouillage des dates.

---

## ✨ Fonctionnalités Principales

### 👤 Espace Employé (Collaborateur)
*   **Authentification sécurisée** : Inscription et connexion (via Laravel Breeze).
*   **Formulaire de Demande intelligent** :
    *   Sélection du type de congé (Congé Annuel, Maladie, Récupération, Événement familial, Maternité, Paternité).
    *   **Contrôle dynamique des durées** : Calcul automatique de la date de fin en fonction de la date de début pour les congés légaux fixes (ex: le champ date de fin se bloque automatiquement sur +3 jours pour la paternité).
    *   **Gestion des justificatifs** : Possibilité d'ajouter un justificatif (PDF, PNG, JPG) avec un bouton **"Effacer"** dynamique si l'employé se trompe de fichier avant de soumettre.
*   **Historique Personnel** : Visualisation en temps réel de ses demandes avec leur statut (Validée, Refusée, En attente) et le commentaire d'explication du manager.

### 👥 Espace Manager (RH)
*   **Redirection automatique** : Redirection vers l'espace de validation lors de la connexion si le compte possède le rôle `manager`.
*   **Compteur d'Absences & Statistiques** : Visualisation du nombre de salariés s'étant absentés au cours d'un mois sélectionné via un filtre calendrier mensuel.
*   **Validation en un clic** : Liste des demandes en attente (`pending`) avec possibilité de les approuver ou de les refuser, agrémentée d'un champ pour saisir un commentaire obligatoire ou facultatif.
*   **Historique Global** : Tableau récapitulatif des demandes traitées au bas du tableau de bord.
*   **Annuaire des Salariés** : Liste de tous les collaborateurs avec leur rôle, le nombre total de congés pris, et accès à une fiche d'édition.
*   **Historique Individuel** : Consultation de l'historique complet et isolé d'un seul salarié directement depuis sa fiche d'édition.

---

## 🔒 Sécurité & Logique Métier (Audit RH)

L'application intègre des contrôles stricts de validation tant au niveau de l'interface qu'au niveau du serveur pour assurer la conformité RH et bloquer toute faille de logique ou d'accès :

*   **Anti-chevauchement de congés** : Blocage automatique côté serveur de toute demande dont la période se superpose à un congé existant approuvé ou en attente pour le même collaborateur.
*   **Interdiction d'auto-validation** : Les managers ne peuvent en aucun cas valider ou refuser leurs propres demandes de congés.
*   **Flexibilité pour les arrêts maladie** : La règle d'interdiction de poser dans le passé (`after_or_equal:today`) est assouplie uniquement pour le type "Congé de Maladie" pour autoriser la déclaration rétroactive d'un certificat sous 48h.
*   **Gestion de la concurrence (Double validation)** : Le serveur s'assure qu'une demande est toujours à l'état `'pending'` avant de la traiter, évitant ainsi les conflits si deux managers modifient le statut d'une même demande simultanément.
*   **Sécurisation Eloquent & CSRF** :
    *   Utilisation de la directive `@csrf` sur l'ensemble des formulaires.
    *   Protection contre le Mass Assignment via le paramétrage des champs `$fillable`.
    *   Prévention contre les injections SQL grâce à l'ORM Eloquent.

---

## 🏛️ Architecture & Stack Technique

### Stack Technique
| Couche | Technologie |
| :--- | :--- |
| **Backend** | PHP 8.2+, Laravel 11 |
| **Frontend** | Blade (Moteur de template), Tailwind CSS |
| **SGBD** | MySQL (en production) / SQLite (en local pour le développement) |
| **Gestionnaires** | Composer (dépendances PHP), npm (dépendances CSS/JS) |
| **Outils** | Laravel Tinker, Git, XAMPP |

### Structure Base de Données
L'application s'appuie sur deux tables clés liées :
1.  **`users`** : Contient les informations des salariés et intègre une colonne `role` (valeurs : `employee` ou `manager`).
2.  **`leaves`** : Liée à la table `users` par une clé étrangère `user_id` avec suppression en cascade. Enregistre le type de congé, les dates, le motif, le chemin du justificatif (`document_path`), le statut (`pending`, `approved`, `rejected`) et le commentaire de validation (`manager_comment`).

---

## 🧪 Tests Automatisés & Assurance Qualité

L'application intègre une suite de **44 tests fonctionnels et unitaires** (PHPUnit / Pest) pour valider l'intégrité de la plateforme. La commande `php artisan test` permet de s'assurer du bon fonctionnement :
*   De la validation des formulaires et du téléversement de documents.
*   Du routage et du contrôle d'accès en fonction des rôles (`employee`/`manager`).
*   Des règles logiques complexes issues de l'audit (chevauchements, auto-validation, dates passées pour maladie, concurrence d'état).

---

## 🛠️ Guide d'Installation & Déploiement

### 💻 1. Déploiement en Environnement de Développement Local

#### Prérequis
*   **PHP** version 8.2 ou supérieure.
*   **Composer** installé.
*   **XAMPP** ou équivalent pour faire tourner PHP/MySQL.
*   **Git** installé.

#### Étapes d'installation
1.  **Cloner le dépôt** :
    ```bash
    git clone <URL_DE_VOTRE_DEPOT>
    cd gestion_conges
    ```
2.  **Installer les dépendances PHP** :
    ```bash
    composer install
    ```
3.  **Créer le fichier de configuration** :
    ```bash
    cp .env.example .env
    ```
4.  **Générer la clé d'application** :
    ```bash
    php artisan key:generate
    ```
5.  **Configurer la base de données** dans votre fichier `.env` :
    ```ini
    DB_CONNECTION=sqlite
    # En SQLite local, le projet utilise database/database.sqlite automatiquement
    ```
6.  **Exécuter les migrations** :
    ```bash
    php artisan migrate
    ```
7.  **Lancer le serveur de développement** :
    ```bash
    php artisan serve
    ```
    L'application est disponible sur [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

### 🌐 2. Guide de Déploiement & Hébergement en Production (Real Hosting Guide)

#### A. Hébergement Mutualisé / cPanel (ex: Hostinger, Namecheap, LWS)

1.  **Préparation & Téléversement du code** :
    *   Compressez votre projet (sans les dossiers `vendor/` et `node_modules/` pour gagner du temps) et téléversez-le via le gestionnaire de fichiers cPanel ou par FTP (FileZilla).
    *   **Document Root** : Configurez votre nom de domaine ou sous-domaine pour qu'il pointe directement vers le sous-dossier `/public` de votre projet Laravel (ex: `/public_html/gestion_conges/public`). *C'est indispensable pour la sécurité.*
2.  **Configuration de l'environnement** :
    *   Éditez le fichier `.env` à la racine de votre projet sur l'hébergeur :
        ```ini
        APP_ENV=production
        APP_DEBUG=false
        APP_URL=https://votre-domaine.com

        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=nom_bdd_hebergeur
        DB_USERNAME=user_bdd_hebergeur
        DB_PASSWORD=mot_de_passe_bdd
        ```
3.  **Commandes de production & Optimisation** :
    *   Si vous avez un accès SSH dans votre cPanel, exécutez les commandes suivantes :
        ```bash
        # Mettre à jour les tables en production en forçant la validation
        php artisan migrate --force

        # Mettre en cache la configuration pour booster les performances
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        ```
4.  **Droits d'accès et sécurité** :
    *   Assurez-vous que les dossiers `storage/` et `bootstrap/cache/` ont des permissions d'écriture suffisantes (normalement `755` ou `775` selon la configuration du serveur web).

#### B. Hébergement VPS / Cloud (ex: Ubuntu + Nginx / Apache + MySQL)

1.  **Installation des composants système** (sur Ubuntu 22.04 LTS par exemple) :
    ```bash
    sudo apt update && sudo apt upgrade -y
    sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-mbstring unzip mysql-server nginx certbot python3-certbot-nginx
    ```
2.  **Configuration de la base de données MySQL** :
    ```sql
    CREATE DATABASE gestion_conges;
    CREATE USER 'conges_user'@'localhost' IDENTIFIED BY 'mot_de_passe_securise';
    GRANT ALL PRIVILEGES ON gestion_conges.* TO 'conges_user'@'localhost';
    FLUSH PRIVILEGES;
    ```
3.  **Configuration de l'hôte virtuel Nginx** (`/etc/nginx/sites-available/gestion_conges`) :
    ```nginx
    server {
        listen 80;
        server_name votre-domaine.com;
        root /var/www/gestion_conges/public;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";

        index index.php;

        charset utf-8;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        error_page 404 /index.php;

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ /\.(?!well-known).* {
            deny all;
        }
    }
    ```
    Activez le site et redémarrez Nginx :
    ```bash
    sudo ln -s /etc/nginx/sites-available/gestion_conges /etc/nginx/sites-enabled/
    sudo systemctl restart nginx
    ```
4.  **Sécurisation HTTPS gratuite (SSL avec Let's Encrypt)** :
    ```bash
    sudo certbot --nginx -d votre-domaine.com
    ```
5.  **Planificateur de tâches Laravel (Cron)** :
    Pour exécuter les événements automatiques en arrière-plan, ajoutez cette ligne dans le crontab de votre serveur (`crontab -e`) :
    ```bash
    * * * * * cd /var/www/gestion_conges && php artisan schedule:run >> /dev/null 2>&1
    ```

---

## 🧪 Procédure pour tester le rôle Manager (via Laravel Tinker)

Par défaut, tous les nouveaux inscrits ont le rôle d'employé. Pour tester les fonctionnalités administratives, vous devez promouvoir un utilisateur au rôle de **manager**.

1.  Ouvrez votre terminal dans le dossier du projet et lancez **Tinker** (l'interpréteur de commandes de Laravel) :
    ```bash
    php artisan tinker
    ```
2.  Recherchez le compte utilisateur créé et attribuez-lui le rôle `manager` (exemple ici avec le premier utilisateur créé) :
    ```php
    $user = App\Models\User::first();
    $user->role = 'manager';
    $user->save();
    ```
3.  Quittez Tinker :
    ```bash
    exit
    ```
    Désormais, lorsque vous vous connecterez avec ce compte, vous serez automatiquement redirigé vers l'espace de validation des congés (Manager).

---

## 👤 Auteure & Licence

*   **Développeuse** : Nouha Kharbouch ([@Nouhakhr](https://github.com/Nouhakhr))
*   **Licence** : Ce projet est sous licence MIT. Libre d'utilisation et de modification dans le cadre scolaire et professionnel.
