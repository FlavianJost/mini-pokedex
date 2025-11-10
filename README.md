# mini-pokedex en PHP POO

## 👤Auteur

**Nom :** Flavian Jost

---

## 📌 Description du projet

Ce projet est un mini Pokédex réalisé en PHP en utilisant la programmation orientée objet (POO).  

Il permet de gérer une liste de Pokémon stockés dans une base de données MySQL.


Fonctionnalités principales :

\- Afficher la liste des Pokémon enregistrés

\- Ajouter un nouveau Pokémon

\- Modifier un Pokémon existant

\- Supprimer un Pokémon

Chaque action est gérée via des fichiers PHP dédiés, avec une structure claire et modulaire.

---

## 📁 Structure du projet

/pokedex-php 

├── classes/ 

│   └── Pokemon.php 

├── config/ 

│   └── database.php

├── logs/ 

├── views/ 

│   ├── list.php

│   ├── edit.php

│   ├── delete.php

│   └── insert.php 

├── autoload.php 

├── index.php 

└── README.md

---

## 🚀 Comment lancer le projet

1. **Placer les fichiers PHP sur un serveur web local**  

&nbsp;  Exemple : dans le dossier `htdocs` de XAMPP ou `www` de WAMP.



2. **Importer la base de données**  

&nbsp;  - Ouvrir un logiciel de gestion de base de données comme **phpMyAdmin** ou **HeidiSQL**

&nbsp;  - Importer le fichier `pokedex.sql`

&nbsp;  - Cela créera la base `pokedex` et la table `pokemon`



3. **Configurer la connexion à la base de données**  

&nbsp;  - Ouvrir le fichier `config/database.php`

&nbsp;  - Modifier les identifiants selon votre configuration locale



4. **Accéder au projet via le navigateur**  

&nbsp;  - Exemple : `http://localhost/pokedex-php/views/list.php`



## 💡 Remarques



\- Le projet utilise un autoload personnalisé pour charger automatiquement les classes du namespace `App`.

\- La classe `Pokemon` encapsule les propriétés et méthodes liées à chaque Pokémon.

\- La base de données est gérée via PDO pour plus de sécurité et de flexibilité.

\- Le code est organisé pour faciliter la maintenance et l’évolution du projet.



---





