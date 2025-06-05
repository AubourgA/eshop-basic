# eShop Projet

Ce projet est une application e-commerce développée avec Symfony en architecture MVC. Il permet aux utilisateurs de parcourir un catalogue de produits, de créer un compte, d'ajouter des produits à un panier, de passer une commande et de payer via Stripe Checkout.

## 📌 Prérequis
Avant de commencer, assurez-vous d'avoir installé les éléments suivants :

- **PHP** >= 8.2  
- **Composer**  
- **Symfony CLI**  
- **Node.js et npm**  
- **Git**  

---

## Fonctionnalités

- **Affichage du catalogue de produits**
- **Inscription et authentification des utilisateurs**
- **Gestion du panier**
- **Création et suivi des commandes**
- **Paiement en ligne avec Stripe Checkout**
- **Choix du transporteur** lors du passage de commande (ex: Colissimo, point relais)
- **Dashboard client** avec résumé et historique des commandes
- **Interface d'administration des produits** (accessible avec un rôle spécifique)
  - Ajout d'informations commerciales : **coût d’achat**, **marge**
  - Visualisation de l'historique changement de prix
  - Ajout badge marketing sur la création/modification fiche produit
  - Visibilité des produits (client ou non)
- **Gestion du stock des produits**
  - Visualisation des quantités disponibles et réservées
  - Ajout de **mouvements de stock** (entrée / sortie) via un formulaire
  - Alerte sur le niveau de stock par rapport à l'indicateur
  

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
Créer un fichier `.env.local` pour y renseigner vos paramètres de base de données :

```sh
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0"
```
### 4️⃣ Création de la base de données
```sh
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```


### 5️⃣Configuration de Stripe
Créez un compte Stripe et obtenez vos clés API. Ajoutez-les à votre fichier .env.local :
```sh
STRIPE_SECRET_KEY="sk_test_votrecle"
STRIPE_PUBLIC_KEY="pk_test_votrecle"
```

### 6️⃣ Lancer le serveur Symfony

```sh
symfony server:start
```

Dans un second terminal, lancez la compilation des styles Tailwind en mode watch :

```sh
php bin/console tailwind:build --watch
```

Votre projet Symfony est maintenant accessible sur http://127.0.0.1:8000.

