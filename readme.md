Pour faire tourner le projet en local :


Charger pour la première fois : 
docker compose up -d --build

Pour les autres lancement : 
docker compose up -d

Démarrer le site web :
docker compose exec php symfony serve -d --allow-all-ip --no-tls --port=8000

(Installer composer)

L'accès en local a PHPmyadmin se fait par l'addresse :
localhost:9000

L'accès en local au site à l'addresse:
localhost:8000

Projet réalisés pour WE4A 
