# eShop Projet  

Ce projet est une application e-commerce développée avec **Symfony 7.1**, **Twig**, **TailwindCSS** et **Symfony UX (Stimulus, Live Components)**.  
Il permet aux utilisateurs de parcourir un catalogue de produits, de créer un compte, d’ajouter des produits à un panier, de passer une commande et de payer via **Stripe Checkout**.  
Une interface d’administration permet de gérer les produits, le stock et les informations commerciales.  

![Présentation de la page d'accueil](doc/images/homePage.png)
---

## 📌 Prérequis  

Avant de commencer, assurez-vous d’avoir installé les éléments suivants :  

- **PHP** >= 8.2  
- **Composer**  
- **Symfony CLI**  
- **Node.js et npm**  
- **Git**  

---

## ✨ Fonctionnalités  

### 🛍️ Côté Client  
- Affichage du **catalogue de produits**  
- **Inscription et authentification** des utilisateurs  
- **Gestion du panier** stocké en session  
  - Modification dynamique des quantités via AJAX  
- **Création et suivi des commandes**  
- **Paiement en ligne avec Stripe Checkout**  
- **Choix du transporteur** lors du passage de commande (Colissimo, point relais, etc.)  
- **Dashboard client** avec :  
  - Résumé (commandes en cours, statut des paiements, adresse par défaut)  
  - Historique des commandes  

---

### ⚙️ Côté Administration  
- **Gestion des produits**  
  - Création et modification via **modales en AJAX (Stimulus + Symfony UX)**  
  - Informations commerciales : **coût d’achat**, **marge**, **badge marketing**  
  - Historique des changements de prix  
  - Gestion de la visibilité (produit visible ou non pour le client)  
- **Gestion du stock**  
  - Visualisation des quantités **disponibles** et **réservées**  
  - Ajout de **mouvements de stock** (entrée / sortie) via formulaire intégré  
  - Alerte automatique sur les seuils de stock  
  - Service `StockManager` pour le calcul en temps réel  
- **Gestion des employés / managers**  
  - Création via modale (Live Components)  
  - **Édition inline** des rôles et déparetemetn directement dans le tableau  

---

### 📊 Dashboards  
- **Dashboard Client** : résumé visuel et historique de commandes  
- **Dashboard Admin** : menu latéral avec graphiques (ventes, stocks, marges, etc.)  

---

## 🚀 Installation  

### 1️⃣ Cloner le projet  
```sh
git clone git@github.com:AubourgA/eshop-basic.git
```

### 2️⃣ Installer les dépendances PHP  
```sh
composer install
```

### 3️⃣ Configurer la base de données  
Créer un fichier `.env.local` et renseigner vos paramètres :  

```sh
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0"
```

### 4️⃣ Créer la base et exécuter les migrations  
```sh
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5️⃣ Configurer Stripe  
Créez un compte Stripe et obtenez vos clés API. Ajoutez-les dans `.env.local` :  

```sh
STRIPE_SECRET_KEY="sk_test_votrecle"
STRIPE_PUBLIC_KEY="pk_test_votrecle"
```

### 6️⃣ Lancer le serveur Symfony  
```sh
symfony server:start
```

Dans un second terminal, lancer la compilation Tailwind en mode watch :  
```sh
php bin/console tailwind:build --watch
```

Votre projet est maintenant accessible sur :  
👉 http://127.0.0.1:8000  


## 📚 Documentation Technique

Pour comprendre la structure du code et les fonctionnalités implémentées, consultez la documentation complète :  

### Sommaire
1. [Préparation de la stack technique](#1️⃣-préparation-de-la-stack-technique)
2. [Architecture du projet](#2️⃣-architecture-du-projet)
3. [Schéma de la base de données](#3️⃣-schéma-de-la-base-de-données)
4. [Dashboard Admin](#4️⃣-dashboard-admin)

