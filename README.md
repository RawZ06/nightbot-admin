# Nightbot Admin

Interface d'administration pour créer et gérer des commandes Nightbot personnalisées avec exécution JavaScript sécurisée.

## Fonctionnalités

- **Authentification sécurisée** : Système de login avec auto-hash des mots de passe (style Yourls)
- **Gestion de commandes** : Interface web SSR pour créer/éditer/supprimer des commandes
- **Éditeur Monaco** : Éditeur de code intégré pour écrire du JavaScript
- **Exécution sécurisée** : Sandbox Deno pour exécuter le code utilisateur en toute sécurité
- **Système de Quotes** : Gestion de listes d'éléments (comme twitch.center/customapi/quote)
- **API REST** : Endpoints pour Nightbot et gestion des quotes
- **Stockage PostgreSQL** : Base de données robuste et performante

## Stack technique

- **Backend**: PHP 8.4 vanilla (pas de framework lourd)
- **Router**: FastRoute
- **Templates**: PHP natif (SSR pur)
- **Base de données**: PostgreSQL 16
- **Exécuteur JS**: Deno (sandbox sécurisé, sans permissions réseau/fichiers)
- **Éditeur**: Monaco Editor (CDN)
- **Authentification**: Système maison avec hash bcrypt auto

## Installation

### Prérequis

- Docker & Docker Compose

### Démarrage

1. **Cloner le projet**
```bash
git clone <repo>
cd papy-nightbot
```

2. **Configurer l'authentification**

```bash
cp .env.example .env
# Éditer .env et configurer ADMIN_USERNAME et ADMIN_PASSWORD
```

3. **Démarrer les conteneurs**
```bash
docker-compose up --build -d
```

4. **Accéder à l'application**
- Interface admin : http://localhost:8080/login
- Login par défaut : `admin` / `changeme` (si utilisé depuis .env.example)
- **⚠️ Changez immédiatement le mot de passe !**

## Structure du projet

```
/public
  /index.php              # Point d'entrée + router
  /static/style.css       # CSS
/src
  /Controllers
    /AdminController.php       # Gestion commandes
    /QuoteController.php       # Gestion quotes
    /QuoteApiController.php    # API publique quotes
    /NightbotController.php    # API Nightbot
    /AuthController.php        # Authentification
  /Models
    /Command.php          # Modèle commande
    /Quote.php            # Modèle quote
  /Executor
    /DenoExecutor.php     # Wrapper Deno pour JS user
  Auth.php                # Système d'authentification
  Config.php              # Gestion de la configuration (env + config.php)
  Database.php            # Connexion PostgreSQL
/views
  /layout.php             # Template principal avec tabs
  /auth/login.php         # Page de login
  /admin/
    index.php             # Liste des commandes
    edit.php              # Éditeur de commande
    quotes.php            # Liste des quotes
    quote_edit.php        # Éditeur de quote
    help.php              # Documentation
init.sql                  # Schéma DB (commands + quotes)
.env.example              # Configuration exemple
docker-compose.yml        # Orchestration
Dockerfile                # Image PHP + Deno
```

## Utilisation

### 1. Créer une commande Nightbot

1. Se connecter sur http://localhost:8080/login
2. Aller dans l'onglet **Commandes**
3. Cliquer sur **Nouvelle commande**
4. Remplir le formulaire :
   - **Nom** : `random` (sans le !)
   - **Description** : "Tire un nombre aléatoire"
   - **Code** :
   ```javascript
   const min = parseInt(args[0]) || 1;
   const max = parseInt(args[1]) || 100;
   const random = Math.floor(Math.random() * (max - min + 1)) + min;
   return `Nombre aléatoire : ${random}`;
   ```
5. Enregistrer

### 2. Appeler depuis Nightbot

Dans Nightbot, créer une commande personnalisée :
```
$(urlfetch http://votre-domaine.com/api/nightbot/random?args=$(querystring))
```

Utilisation dans le chat :
```
!random 1,100
→ Nombre aléatoire : 42
```

### 3. Créer et utiliser des Quotes

#### Créer une quote

1. Aller dans l'onglet **Quotes**
2. Cliquer sur **Nouvelle quote**
3. Nom : `death_messages`
4. Ajouter des éléments :
   - "est mort écrasé par une enclume"
   - "est tombé dans la lave"
   - "a été tué par un zombie"
5. Noter le **token** affiché (ex: `a1b2c3d4e5f6...`)

#### Utiliser une quote dans Nightbot

```
# Commande !death (affiche une mort aléatoire)
$(urlfetch http://votre-domaine.com/api/quote/death_messages/random)
```

Résultat dans le chat :
```
!death
→ est mort écrasé par une enclume
```

## API

### API Nightbot (Commandes)

```http
GET /api/nightbot/{command_name}?args=arg1,arg2,arg3
```

Exécute la commande et retourne le résultat en texte brut.

### API Quotes (Publique)

#### Récupérer un élément aléatoire
```http
GET /api/quote/{quote_name}/random
```

Retourne : `text/plain`

#### Récupérer tous les éléments
```http
GET /api/quote/{quote_name}/all
```

Retourne : `application/json`

#### Ajouter un élément (nécessite token)
```http
POST /api/quote/{quote_name}/add
Content-Type: application/x-www-form-urlencoded

value=nouvel élément&token=VOTRE_TOKEN
```

Retourne : `{"success": true, "item_id": 123}`

#### Supprimer un élément (nécessite token)
```http
POST /api/quote/{quote_name}/remove
Content-Type: application/x-www-form-urlencoded

item_id=123&token=VOTRE_TOKEN
```

Retourne : `{"success": true}`

## Variables disponibles dans le code

Dans votre code JavaScript exécuté par Deno, vous avez accès à :

### `args` - Arguments de la commande

```javascript
// Commande: !random 1,100
// args = ["1", "100"]

const min = parseInt(args[0]) || 1;
const max = parseInt(args[1]) || 100;
```

### Exemples de commandes

#### Boule magique (8ball)
```javascript
const responses = [
  "Oui, absolument !",
  "Non, certainement pas.",
  "Peut-être...",
  "Demande à nouveau plus tard."
];
const random = Math.floor(Math.random() * responses.length);
return responses[random];
```

#### Choix aléatoire
```javascript
// Usage: !choose pizza,burger,tacos
const choices = args[0].split(',').map(s => s.trim());
const random = Math.floor(Math.random() * choices.length);
return `Je choisis : ${choices[random]} !`;
```

#### Appel API externe avec fetch
```javascript
// Usage: !weather Paris
const city = args[0] || 'Paris';

try {
  const response = await fetch(`https://api.example.com/weather?city=${city}`);
  const data = await response.json();
  return `Météo à ${city}: ${data.temp}°C, ${data.description}`;
} catch (error) {
  return `Impossible de récupérer la météo pour ${city}`;
}
```

## Sécurité

### Sandbox Deno

Le code JavaScript est exécuté dans un sandbox Deno sécurisé :
- ✅ **Accès réseau** : fetch() et API HTTPS disponibles (--allow-net)
- ❌ **Pas d'accès fichiers** : lecture/écriture interdite
- ❌ **Pas d'accès environnement** : variables env protégées
- ⏱️ **Timeout de 5 secondes** maximum
- ✅ **Isolation totale** du processus

### Authentification

- Mots de passe hashés avec `bcrypt`
- Auto-hash au premier login (comme Yourls)
- Sessions PHP sécurisées
- Protection de toutes les routes admin

### Tokens Quotes

- Tokens générés aléatoirement (32 caractères hex)
- Requis pour ajouter/supprimer des éléments
- Stockés dans la base de données

## Configuration

### Variables d'environnement

Dans `.env` ou `docker-compose.yml` :

```bash
# Base de données
DB_HOST=postgres
DB_PORT=5432
DB_NAME=nightbot
DB_USER=nightbot_user
DB_PASSWORD=nightbot_password

# Authentification
ADMIN_USERNAME=admin
ADMIN_PASSWORD=changeme

# API Token (optionnel)
API_TOKEN=your-secret-token-here
```

**Note :** Les mots de passe en clair seront automatiquement hashés au premier login et stockés directement dans `src/Config.php`.

### Variables disponibles

- `DB_HOST` : hôte PostgreSQL (défaut: `postgres`)
- `DB_PORT` : port PostgreSQL (défaut: `5432`)
- `DB_NAME` : nom de la base (défaut: `nightbot`)
- `DB_USER` : utilisateur DB (défaut: `nightbot_user`)
- `DB_PASSWORD` : mot de passe DB
- `ADMIN_USERNAME` : nom d'utilisateur admin (requis)
- `ADMIN_PASSWORD` : mot de passe admin (requis)
- `API_TOKEN` : token API (optionnel)
- `APP_ENV` : environnement (défaut: `production`)
- `APP_DEBUG` : mode debug (défaut: `false`)

## Développement

### Logs

```bash
docker-compose logs -f app
```

### Accéder à la DB

```bash
docker-compose exec postgres psql -U nightbot_user -d nightbot
```

### Rebuild après modifications

```bash
docker-compose down
docker-compose up --build -d
```

## Production

Pour déployer en production :

1. **Changer les credentials PostgreSQL** dans `.env`
2. **Changer le mot de passe admin** dans `config.php`
3. **Mettre un reverse proxy** (Nginx/Caddy/Traefik) devant
4. **Activer HTTPS** obligatoire
5. **Configurer des sauvegardes PostgreSQL** automatiques
6. **Monitorer les logs** d'erreurs
7. **Limiter le rate-limiting** sur les endpoints publics

## Dépannage

### Les quotes ne se créent pas

Vérifier que les migrations SQL ont été appliquées :
```bash
docker-compose exec postgres psql -U nightbot_user -d nightbot -c "\dt"
```

Vous devriez voir les tables `commands`, `quotes` et `quote_items`.

### Le code Deno ne s'exécute pas

Vérifier que Deno est bien installé dans le conteneur :
```bash
docker-compose exec app deno --version
```

### Erreur d'authentification

1. Vérifier que les variables `ADMIN_USERNAME` et `ADMIN_PASSWORD` sont définies dans `.env`
2. Vérifier que `src/Config.php` est accessible en écriture (pour l'auto-hash)
3. Vérifier les logs : `docker-compose logs app`

## License

MIT
