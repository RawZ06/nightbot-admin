# Message.split() - Envoyer plusieurs messages

## Vue d'ensemble

La classe `Message` permet de découper facilement tes réponses en plusieurs messages dans Nightbot, directement depuis ton code JavaScript.

**Avantages :**
- ✅ Un seul `$(urlfetch)` dans Nightbot
- ✅ Pas besoin de créer de quote
- ✅ Pas de stockage en base de données
- ✅ Contrôle total dans ton code JS
- ✅ Simple et intuitif
- ✅ Utilise vxrl.xyz en arrière-plan

## Utilisation de base

### Dans ton code JavaScript

```javascript
// Au lieu de retourner un long message
return "Message très très très long qui dépasse 400 caractères...";

// Utilise Message.split()
return Message.split(
    "Première partie du message",
    "Deuxième partie du message",
    "Troisième partie du message"
);
```

### Dans Nightbot

Configure ta commande normalement avec UN SEUL `$(urlfetch)` :

```
!macommande
$(urlfetch http://ton-domaine.com/api/nightbot/macommande)
```

**Comment ça marche :**
1. `Message.split()` génère automatiquement une URL vxrl.xyz
2. Cette URL contient tous tes messages encodés
3. Le résultat retourné est `$(urlfetch https://vxrl.xyz/msg1/msg2/msg3?i=5000&d=1)`
4. Nightbot exécute ce urlfetch imbriqué
5. vxrl.xyz envoie les messages séparément avec un intervalle de 5 secondes

## Exemples pratiques

### Exemple 1 : Messages simples

```javascript
// Commande: !welcome
return Message.split(
    "Bienvenue sur le stream !",
    "N'hésite pas à follow si tu aimes le contenu",
    "Et rejoins le Discord pour discuter !"
);
```

**Résultat dans le chat :**
```
[Bot] Bienvenue sur le stream !
[Bot] N'hésite pas à follow si tu aimes le contenu
[Bot] Et rejoins le Discord pour discuter !
```

### Exemple 2 : Personnaliser l'intervalle

```javascript
// Envoyer les messages avec 3 secondes d'intervalle au lieu de 5
return Message.split(
    "Message 1",
    "Message 2",
    "Message 3",
    { interval: 3000 }  // 3000ms = 3 secondes
);
```

### Exemple 3 : Liste de paramètres

```javascript
// Commande: !settings
const settings = [
    "Auto-host: ON",
    "Slow mode: 10s",
    "Followers only: OFF",
    "Subs mode: OFF"
];

return Message.split(
    "⚙️ Paramètres actuels:",
    settings.slice(0, 2).join(" | "),
    settings.slice(2).join(" | ")
);
```

**Résultat dans le chat :**
```
[Bot] ⚙️ Paramètres actuels:
[Bot] Auto-host: ON | Slow mode: 10s
[Bot] Followers only: OFF | Subs mode: OFF
```

### Exemple 4 : Découpage intelligent

```javascript
// Générer un long message
const users = ["Alice", "Bob", "Charlie", "David", "Eve", "Frank"];
const message = "Top viewers: " + users.join(", ");

// Découper si trop long
if (message.length > 200) {
    const half = Math.ceil(users.length / 2);
    return Message.split(
        "Top viewers (1/2): " + users.slice(0, half).join(", "),
        "Top viewers (2/2): " + users.slice(half).join(", ")
    );
}

return message;
```

### Exemple 5 : Avec conditions

```javascript
// Commande: !help [commande]
const command = args[0];

if (command === 'quote') {
    return Message.split(
        "📝 !quote - Gestion des citations",
        "Usage: !quote add <texte> | !quote random",
        "Exemples: !quote add Belle journée | !quote 5"
    );
}

if (command === 'game') {
    return Message.split(
        "🎮 !game - Changer le jeu",
        "Usage: !game <nom du jeu>",
        "Exemple: !game Just Chatting"
    );
}

// Help général
return Message.split(
    "💡 Commandes disponibles:",
    "!quote, !game, !song, !uptime",
    "Tape !help <commande> pour plus d'infos"
);
```

## Options avancées

### Paramètres disponibles

```javascript
Message.split(
    "Message 1",
    "Message 2",
    "Message 3",
    {
        interval: 5000,  // Intervalle entre les messages en millisecondes (défaut: 5000)
        delay: 1         // Délai avant le premier message en secondes (défaut: 1)
    }
);
```

### Exemple avec options personnalisées

```javascript
// Messages rapides avec un délai plus court
return Message.split(
    "Compte à rebours:",
    "3...",
    "2...",
    "1...",
    "GO !",
    { interval: 1000, delay: 0 }  // 1 seconde entre chaque, pas de délai initial
);
```

## Points importants

### ✅ À faire

- Limite-toi à 5-7 messages maximum pour éviter le spam
- Ajoute des numéros (1/3, 2/3, 3/3) pour la clarté quand c'est pertinent
- Utilise un intervalle raisonnable (minimum 1 seconde recommandé)

### ❌ À éviter

- Ne pas utiliser avec des messages courts (< 400 caractères total)
- Ne pas envoyer trop de messages d'un coup (spam)
- Ne pas mettre un intervalle trop court (< 500ms)

## Fonctionnement technique

1. Tu retournes `Message.split("msg1", "msg2", "msg3")`
2. Le code JavaScript génère une URL vxrl.xyz encodée
3. Le résultat retourné est : `$(urlfetch https://vxrl.xyz/msg1/msg2/msg3?i=5000&d=1)`
4. Nightbot exécute ce `$(urlfetch)` imbriqué
5. vxrl.xyz traite l'URL et envoie les messages avec l'intervalle spécifié
6. Les messages apparaissent un par un dans le chat

## Compatibilité avec les strings classiques

Tu peux mixer les deux approches :

```javascript
// Simple
if (args.length === 0) {
    return "Usage: !cmd <arg>";
}

// Multiple si nécessaire
if (result.length > 300) {
    return Message.split(
        result.slice(0, 300),
        result.slice(300)
    );
}

// Simple
return result;
```

## Exemple complet

```javascript
// Commande: !top
const topViewers = [
    "Alice (120h)", "Bob (95h)", "Charlie (87h)",
    "David (76h)", "Eve (65h)", "Frank (54h)",
    "Grace (45h)", "Henry (38h)"
];

// Découper en groupes de 3
const chunks = [];
for (let i = 0; i < topViewers.length; i += 3) {
    const group = topViewers.slice(i, i + 3).join(" | ");
    const num = Math.floor(i / 3) + 1;
    const total = Math.ceil(topViewers.length / 3);
    chunks.push(`🏆 Top viewers (${num}/${total}): ${group}`);
}

return Message.split(...chunks, { interval: 4000 });
```

**Dans Nightbot :**
```
!top
$(urlfetch http://ton-domaine.com/api/nightbot/top)
```

**Résultat :**
```
[Bot] 🏆 Top viewers (1/3): Alice (120h) | Bob (95h) | Charlie (87h)
[Bot] 🏆 Top viewers (2/3): David (76h) | Eve (65h) | Frank (54h)
[Bot] 🏆 Top viewers (3/3): Grace (45h) | Henry (38h)
```

## URL générée

Quand tu utilises `Message.split()`, voici ce qui se passe en arrière-plan :

```javascript
Message.split("Hello", "World", { interval: 5000, delay: 1 })
// Génère:
// $(urlfetch https://vxrl.xyz/Hello/World?i=5000&d=1)
```

Les messages sont automatiquement encodés pour l'URL (espaces, caractères spéciaux, etc.).
