# Projet WE4B - Plateforme d'Application Hybride (Symfony / Angular)

Ce dépôt contient l'infrastructure complète du projet fonctionnant sous Docker. La stack comprend un backend Symfony, un frontend Angular, ainsi que des bases de données MySQL et MongoDB avec leurs interfaces de gestion respectives.

---

## Prérequis

Avant de commencer, assurez-vous d'avoir installé sur votre machine :
* Docker et Docker Compose
* Les ports suivants libres sur votre machine hôte : 8000, 4200, 9000, 27017, 8081

---

## Procédure d'Installation (First Setup)

Suivez scrupuleusement ces étapes pour lancer l'application la première fois :

### 1. Cloner le projet et lancer les conteneurs
Démarrez la stack Docker en arrière-plan. Cela va construire les images et configurer les volumes réseaux :
```bash
docker-compose up -d --build
```

---

### 2. Cloner le projet et lancer les conteneurs

```bash
docker exec -it php-back composer install
```

---

### 3. Démarrer le serveur Web interne

```bash
docker exec -d php-back php -S 0.0.0.0:8000 -t public
```

### 4. Vider et générer le cache Symfony

```bash
docker exec -it php-back php bin/console cache:clear
```