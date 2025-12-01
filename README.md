# 24hWeather — Site météo en ligne

Projet de « Développement Web Avancé » (3ᵉ année Licence Informatique, Cergy Paris Université) — site Web de météo avec comptes utilisateurs, carte interactive, favoris, et prévisions jusqu’à 8 jours.

Site en ligne : [https://24hweather.alwaysdata.net/](https://24hweather.alwaysdata.net/)

---

## 📌 Sommaire

- [24hWeather — Site météo en ligne](#24hweather--site-météo-en-ligne)
	- [📌 Sommaire](#-sommaire)
	- [🎯 Objectif du projet](#-objectif-du-projet)
	- [✨ Fonctionnalités](#-fonctionnalités)
	- [🧩 Structure du projet / Architecture](#-structure-du-projet--architecture)
	- [🚀 Installation \& Déploiement](#-installation--déploiement)
		- [Prérequis](#prérequis)
		- [Étapes d’installation](#étapes-dinstallation)
	- [🛠️ Technologies \& Outils utilisés](#️-technologies--outils-utilisés)
	- [👥 Auteur \& Licence](#-auteur--licence)

---

## 🎯 Objectif du projet

Le but de 24hWeather est de fournir un **site web complet de consultation météo** à toute personne — permettant de :

- Consulter les conditions météo actuelles.
- Consulter des **prévisions horaires (jusqu’à 24h)**.
- Consulter des **prévisions sur 8 jours**.  
- Chercher la météo d’une ville.  
- Consulter la météo mondiale via une carte interactive.  
- Créer un compte utilisateur, gérer son profil et ses **villes favorites**.  
- Planifier des **alertes / rappels météo** (mail) pour un voyage, un rendez-vous, etc.

---

## ✨ Fonctionnalités

- Recherche de la météo d’une ville par nom.  
- Carte interactive permettant de visualiser la météo mondiale.  
- Gestion d’un compte utilisateur (inscription, connexion, profil).  
- Ajout de villes favorites pour un accès rapide aux prévisions.  
- Prévisions météo : actuelles, horaires (24h), et 8 jours.  
- Option de programmation de **rappels météo personnalisés** (par e-mail).  
- Gestion des événements / rappels météo via un petit calendrier.
- Offrir un Responsive design pour compatibilité mobile / desktop.

---

## 🧩 Structure du projet / Architecture

Le projet contient plusieurs dossiers et fichiers, notamment :

```text
/ (racine du projet)
 ├── class/         # Classes PHP — logique métier
 ├── include/       # Fichiers PHP inclus, utilitaires, fonctions partagées 
 ├── leaflet/       # Librairie JS libérant carte interactive
 ├── images/ fonts/ # Ressources visuelles
 ├── styles.css / styles.min.css                                    # Feuilles de style
 ├── script.js / script.min.js                                      # Scripts JS front-end
 ├── meteo.php, world.php, profil.php, login.php, logout.php …      # Fichiers PHP pages principales
 ├── addevent.php, editevent.php, event.php …                       # Gestion des rappels / événements météo
 ├── city.list.json  # Données de villes (utilisées pour recherche)  
 ├── ddl.sql         # Script SQL pour la création de la base de données  
 └── …               # Autres ressources, AJAX, traitement, etc.  
```

Cette structure sépare la **logique serveur (PHP, base de données)**, la **présentation (HTML/CSS/JS)** et les **ressources front/back**, pour un projet modulaire et maintenable.

---

## 🚀 Installation & Déploiement

### Prérequis  

- Serveur web PHP + MySQL (ou compatible)  
- Possibilité d’utiliser composer (selon la configuration)  

### Étapes d’installation  

1. Cloner le dépôt :

   ```bash
   git clone https://github.com/nchrismant/24hWeather.git
   cd 24hWeather
   ```

2. Importer la base de données MySQL à partir du fichier `ddl.sql`.
3. Configurer l’accès à la base de données dans un fichier de configuration (dans une classe PHP à mettre dans le dossier `class/`).
4. Déployer les fichiers sur votre serveur (ex : via FTP, SFTP ou un hébergement type Alwaysdata).
5. Accéder au site via un navigateur pour s’inscrire / se connecter, puis utiliser les fonctionnalités.

---

## 🛠️ Technologies & Outils utilisés

| Technologie         | Rôle              |
| ------------------- | ----------------- |
| **PHP**             | Langage principal côté serveur |
| **MySQL**           | Base de données |
| **JavaScript**      | Interactions côté client, AJAX, gestion de la carte météo avec Leaflet |
| **HTML / CSS**      | Interface web (avec deux feuilles de style : standard & alternatif) |
| **Hébergement web** | Déploiement du site |

## 👥 Auteur & Licence

- **Auteur** : Nathan Chrismant — Étudiant L3 Informatique, Cergy Paris Université.

Projet distribué sous licence **Open Source**.
