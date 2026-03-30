# TP Iran - Frontoffice / Backoffice

Projet PHP (Apache + MySQL) avec deux applications:

- Frontoffice: actualites publiques
- Backoffice: administration des articles et categories

## Prerequis

- Docker
- Docker Compose

## Lancement rapide

```powershell
docker compose up --build
```

## Acces

- Frontoffice: http://localhost:8080
- Backoffice: http://localhost:8081
- MySQL: localhost:3307

## Identifiants seed (backoffice)

- Utilisateur: `admin`
- Mot de passe: `admin123`

## Variables de connexion BDD

Le projet utilise les variables suivantes (deja configurees dans `docker-compose.yml`):

- `DB_HOST=db`
- `DB_PORT=3306`
- `DB_NAME=guerre_iran`
- `DB_USER=app`
- `DB_PASSWORD=app_password`

## Notes

- Le schema SQL est initialise via `database/init.sql` au premier demarrage de MySQL.
- Si le volume `db_data` existe deja, supprimer le volume pour reinitialiser la base.

