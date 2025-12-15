# 📊 Personal Finance Dashboard – README

Bienvenue dans le **Personal Finance Dashboard**, un mini-projet développé pour une startup locale souhaitant offrir aux utilisateurs un outil simple, clair et intuitif pour gérer leurs finances personnelles.

Ce tableau de bord permet de suivre les **revenus**, les **dépenses**, et d’obtenir une vue globale du **budget**. Il est construit avec **PHP** & **MySQL** pour garantir performance, simplicité et accessibilité.

---

## 🌟 Fonctionnalités Principales

### 🔹 Gestion des Revenus (Incomes)

* Affichage de tous les revenus dans un tableau propre et lisible.
* Formulaire pour ajouter un nouveau revenu.
* Insertion en base de données via **INSERT**.
* Modification d’un revenu existant via un formulaire dédié.
* Mise à jour en base via **UPDATE**.
* Suppression d’un revenu via **DELETE**.
* Validation des données avant insertion (montant, date, description).

### 🔹 Gestion des Dépenses (Expenses)

* Affichage de toutes les dépenses sous forme de tableau.
* Ajout d’une nouvelle dépense via un formulaire.
* Enregistrement en base via **INSERT**.
* Modifications possibles via un formulaire.
* Mise à jour via **UPDATE**.
* Suppression via **DELETE**.
* Validation des données avant insertion.

---

## 🗄️ Base de Données – SQL

Toutes les requêtes nécessaires sont regroupées dans le fichier **database.sql**.

### Contenu :

* Création de la base de données.
* Création de la table `incomes`.
* Création de la table `expenses`.
* Ajout des **clés primaires**.
* Définition des bons types SQL :

  * `DECIMAL` pour les montants
  * `DATE` pour les dates
  * `VARCHAR/TEXT` pour les descriptions
* Ajout des contraintes : `NOT NULL`, `DEFAULT`, etc.

---

## 📈 Dashboard – Résumé Financier

Le tableau de bord affiche :

* ✔️ Total des revenus
* ✔️ Total des dépenses
* ✔️ Solde actuel (**revenus – dépenses**)
* ✔️ Revenus et dépenses du **mois en cours**
* 📊 Un graphique simple (optionnel) pour visualiser le budget

---

## 🚀 Technologies Utilisées

* **PHP** (Back-End)
* **MySQL** (Base de données)
* **HTML / CSS / JS** (Interface utilisateur)
* **Chart.js** (Graphique optionnel)

---

## 🎯 Objectif du Projet

Fournir une première version opérationnelle du système de gestion financière personnelle, prête à être utilisée et évolutive pour les futures versions.

Ce README résume les user stories, les fonctionnalités développées et la structure globale du projet.

---

## 🖼️ Use Case Diagram

 **[diagramme de cas d'utilisation](https://lucid.app/lucidchart/04668aad-a5eb-474c-a344-1c18ef6b6adf/edit?viewport_loc=401%2C666%2C1015%2C454%2C.Q4MUjXso07N&invitationId=inv_4b0f84a3-e795-4a94-ae8a-3625f3c7c3e7)** 


---

Si vous souhaitez améliorer l’interface, ajouter de nouvelles métriques ou intégrer des fonctionnalités avancées (catégories, export PDF, IA…), ce projet est entièrement évolutif.
