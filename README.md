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
    *   Possibilité d'ajouter un **justificatif** (PDF, PNG, JPG) avec un bouton **"Effacer"** dynamique si l'employé se trompe de fichier avant de soumettre.
*   **Historique Personnel** : Visualisation en temps réel de ses demandes avec leur statut (Validée, Refusée, En attente) et le commentaire d'explication du manager.

### 👥 Espace Manager (RH)
*   **Redirection automatique** : Redirection vers l'espace de validation lors de la connexion si le compte possède le rôle `manager`.
*   **Compteur d'Absences & Statistiques** : Visualisation du nombre de salariés s'étant absentés au cours d'un mois sélectionné via un filtre calendrier mensuel.
*   **Validation en un clic** : Liste des demandes en attente (`pending`) avec possibilité de les approuver ou de les refuser, agrémentée d'un champ pour saisir un commentaire obligatoire ou facultatif.
*   **Historique Global** : Tableau récapitulatif des demandes traitées au bas du tableau de bord.
*   **Annuaire des Salariés** : Liste de tous les collaborateurs avec leur rôle, le nombre total de congés pris, et accès à une fiche d'édition.
*   **Historique Individuel** : Consultation de l'historique complet et isolé d'un seul salarié directement depuis sa fiche d'édition.

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
1.  **`users`** : Enrichie d'une colonne `role` (valeurs : `employee` ou `manager`).
2.  **`leaves`** : Liée à la table `users` par une clé étrangère `user_id` avec suppression en cascade. Elle enregistre les types, motifs, justificatifs, dates, statuts et commentaires de validation.

---

## 🔒 Sécurité Implémentée

*   **Protection CSRF** : Protection systématique de tous les formulaires contre les attaques de type Cross-Site Request Forgery via la directive `@csrf`.
*   **Middlewares d'accès** : Protection des routes sensibles (`auth`, `verified`) pour garantir que seules les personnes autorisées accèdent aux tableaux de bord.
*   **Sécurisation Eloquent** :
    *   Prévention contre le Mass Assignment grâce à la définition stricte des champs `$fillable` dans les modèles.
    *   Prévention contre les injections SQL grâce à l'utilisation systématique des requêtes préparées de l'ORM Eloquent.
*   **Validation stricte côté serveur** : Recalcul et validation stricte de la cohérence des dates et des durées imposées par le contrôleur back-end PHP pour empêcher toute manipulation frauduleuse du code HTML client.

---

## 🛠️ Guide d'Installation (En local)

### Prérequis
*   **PHP** version 8.2 ou supérieure.
*   **Composer** installé sur votre machine.
*   **XAMPP** ou un serveur local équivalent.
*   **Git** installé.

### Étapes d'installation
1.  **Cloner le dépôt** :
    ```bash
    git clone <URL_DE_VOTRE_DEPOT>
    cd gestion_conges
    ```

2.  **Installer les dépendances Composer (PHP)** :
    ```bash
    composer install
    ```

3.  **Créer le fichier de configuration d'environnement** :
    ```bash
    cp .env.example .env
    ```

4.  **Générer la clé de l'application** :
    ```bash
    php artisan key:generate
    ```

5.  **Configurer la base de données** :
    Ouvrez le fichier `.env` et ajustez les lignes correspondantes (ex. pour SQLite ou MySQL) :
    ```ini
    DB_CONNECTION=sqlite
    # Si SQLite, le projet utilise automatiquement le fichier database/database.sqlite
    ```

6.  **Exécuter les migrations de tables** :
    ```bash
    php artisan migrate
    ```

7.  **Lancer le serveur de développement local** :
    ```bash
    php artisan serve
    ```
    L'application sera accessible à l'adresse suivante : [http://127.0.0.1:8000](http://127.0.0.1:8000).

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
