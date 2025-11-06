<div class="max-w-4xl mx-auto space-y-8">
    <h2>Guide d'utilisation - Écriture de code pour Nightbot</h2>

    <section class="help-section">
        <h3>Variables et objets disponibles</h3>
        <p>Dans votre code JavaScript, vous avez accès aux éléments suivants :</p>

        <div class="code-example">
            <h4><code>args</code> - Arguments de la commande</h4>
            <p>Un tableau contenant les arguments passés après la commande dans Nightbot.</p>
            <pre><code>// Commande: !random 1,100
// args = ["1", "100"]

const min = parseInt(args[0]) || 1;
const max = parseInt(args[1]) || 100;
const random = Math.floor(Math.random() * (max - min + 1)) + min;
return `Nombre aléatoire : ${random}`;</code></pre>
        </div>

        <div class="code-example">
            <h4><code>Quote</code> - Classe statique pour accéder aux quotes</h4>
            <p>Permet d'accéder aux quotes créées dans l'onglet Quotes (API avec chaînage style Java).</p>

            <strong>Méthode statique :</strong>
            <ul style="margin-top: 0.5rem; padding-left: 1.5rem;">
                <li><code>Quote.name(nom)</code> - Spécifie le nom de la quote (retourne un builder)</li>
            </ul>

            <strong>Méthodes du builder (après <code>Quote.name(...)</code>) :</strong>
            <ul style="margin-top: 0.5rem; padding-left: 1.5rem;">
                <li><code>.load(index)</code> - Charge un élément par index (number)</li>
                <li><code>.load(texte)</code> - Charge un élément qui contient le texte (string)</li>
                <li><code>.all()</code> - Récupère tous les éléments</li>
                <li><code>.random()</code> - Récupère un élément aléatoire</li>
                <li><code>.count()</code> - Compte le nombre d'éléments</li>
                <li><code>.exists()</code> - Vérifie si la quote existe</li>
            </ul>

            <pre><code>// Exemple 1: Charger par index
const death = Quote.name('death_messages').load(0);
return death; // Premier élément

// Exemple 2: Charger par recherche de texte
const death = Quote.name('death_messages').load('enclume');
return death; // "est mort écrasé par une enclume"

// Exemple 3: Élément aléatoire
const death = Quote.name('death_messages').random();
return death;

// Exemple 4: Tous les éléments
const allDeaths = Quote.name('death_messages').all();
return allDeaths.join(' | ');</code></pre>
        </div>
    </section>

    <section class="help-section">
        <h3>Utilisation des Quotes</h3>
        <p>Les quotes vous permettent de stocker et récupérer des listes d'éléments.</p>

        <div class="code-example">
            <h4>Créer une quote</h4>
            <ol>
                <li>Allez dans l'onglet <strong>Quotes</strong></li>
                <li>Cliquez sur <strong>Nouvelle quote</strong></li>
                <li>Donnez un nom à votre quote (ex: <code>death_messages</code>)</li>
                <li>Ajoutez des éléments dans la quote</li>
                <li>Notez le <strong>token</strong> affiché</li>
            </ol>
        </div>

        <div class="code-example">
            <h4>Utiliser une quote dans votre code</h4>
            <p>Les quotes sont directement accessibles dans votre code JavaScript via la classe <code>Quote</code> :</p>
            <pre><code>// Récupérer un élément aléatoire
const death = Quote.name('death_messages').random();
return `${args[0]} ${death}`;

// Récupérer tous les éléments
const allDeaths = Quote.name('death_messages').all();
return `Il y a ${allDeaths.length} façons de mourir`;

// Compter les éléments
const count = Quote.name('death_messages').count();
return `${count} messages disponibles`;

// Vérifier si une quote existe
if (!Quote.name('greetings').exists()) {
    return 'Aucune quote disponible';
}
return Quote.name('greetings').random();

// Charger un élément spécifique par index
const firstDeath = Quote.name('death_messages').load(0);
return firstDeath;

// Chercher un élément qui contient un texte
const enclumeDeath = Quote.name('death_messages').load('enclume');
return enclumeDeath;</code></pre>
        </div>

        <div class="code-example">
            <h4>Exemple complet : Commande !death</h4>
            <pre><code>// Usage: !death Joueur123
const playerName = args[0] || 'Quelqu'un';

// Vérifier que la quote existe
if (!Quote.name('death_messages').exists()) {
    return 'Aucun message de mort disponible';
}

// Récupérer un message aléatoire
const death = Quote.name('death_messages').random();
return `${playerName} ${death}`;</code></pre>
        </div>
    </section>

    <section class="help-section">
        <h3>API Quotes - Référence complète</h3>

        <div class="api-reference">
            <div class="api-endpoint">
                <h4>GET /api/quote/{name}/random</h4>
                <p>Récupère un élément aléatoire de la quote</p>
                <pre><code>// Exemple
GET /api/quote/death_messages/random

// Réponse (text/plain)
est mort écrasé par une enclume</code></pre>
            </div>

            <div class="api-endpoint">
                <h4>GET /api/quote/{name}/all</h4>
                <p>Récupère tous les éléments de la quote</p>
                <pre><code>// Exemple
GET /api/quote/death_messages/all

// Réponse (application/json)
[
  {
    "id": 1,
    "quote_id": 1,
    "value": "est mort écrasé par une enclume",
    "created_at": "2025-01-05 12:00:00"
  },
  ...
]</code></pre>
            </div>

            <div class="api-endpoint">
                <h4>POST /api/quote/{name}/add</h4>
                <p>Ajoute un élément à la quote (nécessite le token)</p>
                <pre><code>// Exemple
POST /api/quote/death_messages/add
Content-Type: application/x-www-form-urlencoded

value=est tombé dans la lave&token=VOTRE_TOKEN

// Réponse (application/json)
{
  "success": true,
  "item_id": 5
}</code></pre>
            </div>

            <div class="api-endpoint">
                <h4>POST /api/quote/{name}/remove</h4>
                <p>Supprime un élément de la quote (nécessite le token)</p>
                <pre><code>// Exemple
POST /api/quote/death_messages/remove
Content-Type: application/x-www-form-urlencoded

item_id=5&token=VOTRE_TOKEN

// Réponse (application/json)
{
  "success": true
}</code></pre>
            </div>
        </div>
    </section>

    <section class="help-section">
        <h3>Exemples de commandes</h3>

        <div class="code-example">
            <h4>Commande !8ball (boule magique)</h4>
            <pre><code>const responses = [
  "Oui, absolument !",
  "Non, certainement pas.",
  "Peut-être...",
  "Demande à nouveau plus tard.",
  "C'est certain !",
  "Je n'en suis pas sûr."
];

const random = Math.floor(Math.random() * responses.length);
return responses[random];</code></pre>
        </div>

        <div class="code-example">
            <h4>Commande !calc (calculatrice)</h4>
            <pre><code>// Usage: !calc 5+3*2
const expression = args.join('');

try {
  // Évaluer l'expression mathématique de manière sécurisée
  const result = Function(`'use strict'; return (${expression})`)();
  return `${expression} = ${result}`;
} catch (error) {
  return "Expression invalide";
}</code></pre>
        </div>

        <div class="code-example">
            <h4>Commande !choose (choix aléatoire)</h4>
            <pre><code>// Usage: !choose pizza,burger,tacos
if (args.length === 0) {
  return "Usage: !choose option1,option2,option3";
}

const choices = args[0].split(',').map(s => s.trim());
const random = Math.floor(Math.random() * choices.length);
return `Je choisis : ${choices[random]} !`;</code></pre>
        </div>

        <div class="code-example">
            <h4>Commande !weather (avec fetch API)</h4>
            <pre><code>// Usage: !weather Paris
const city = args[0] || 'Paris';

try {
  const response = await fetch(`https://api.example.com/weather?city=${city}`);
  const data = await response.json();
  return `Météo à ${city}: ${data.temp}°C, ${data.description}`;
} catch (error) {
  return `Impossible de récupérer la météo pour ${city}`;
}</code></pre>
        </div>
    </section>

    <section class="help-section">
        <h3>Permissions et limitations</h3>
        <ul>
            <li>✅ <strong>Accès réseau</strong> : fetch() et API HTTPS disponibles</li>
            <li>❌ <strong>Système de fichiers</strong> : Pas d'accès lecture/écriture</li>
            <li>❌ <strong>Variables d'environnement</strong> : Pas d'accès aux env vars</li>
            <li>⏱️ <strong>Timeout</strong> : Exécution limitée à 5 secondes maximum</li>
        </ul>
        <p>Le code s'exécute dans un sandbox Deno sécurisé avec accès réseau pour les API externes.</p>
    </section>
</div>

<style>
h2 {
    font-size: 1.875rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
}

.help-section {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.help-section h3 {
    font-size: 1.25rem;
    font-weight: 700;
    border-bottom: 2px solid rgb(168 85 247);
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.help-section p {
    color: rgb(55 65 81);
    line-height: 1.6;
}

.code-example {
    background: rgb(249 250 251);
    padding: 1rem;
    border-radius: 0.5rem;
    border-left: 4px solid rgb(59 130 246);
    margin: 1rem 0;
}

.code-example h4 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.code-example code {
    padding: 0.125rem 0.5rem;
    background: rgb(229 231 235);
    color: rgb(168 85 247);
    border-radius: 0.25rem;
    font-size: 0.875rem;
    font-family: monospace;
}

.code-example pre {
    background: rgb(31 41 55);
    color: rgb(243 244 246);
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 0.5rem 0;
}

.code-example pre code {
    background: transparent;
    color: rgb(243 244 246);
    padding: 0;
}

.api-reference {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.api-endpoint {
    background: white;
    border: 1px solid rgb(229 231 235);
    border-radius: 0.5rem;
    padding: 1rem;
}

.api-endpoint h4 {
    font-size: 1rem;
    font-weight: 700;
    color: rgb(239 68 68);
    font-family: monospace;
    background: rgb(243 244 246);
    padding: 0.5rem 0.75rem;
    border-radius: 0.25rem;
    margin-bottom: 0.5rem;
}

.api-endpoint p {
    font-size: 0.875rem;
    color: rgb(107 114 128);
}

.help-section ul {
    list-style: disc;
    list-style-position: inside;
    color: rgb(55 65 81);
    line-height: 2;
}

.help-section ol {
    list-style: decimal;
    list-style-position: inside;
    color: rgb(55 65 81);
    line-height: 2;
    padding-left: 1.5rem;
}

.help-section strong {
    font-weight: 600;
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    .dark .help-section {
        background: rgb(31 41 55);
    }

    .dark .help-section p,
    .dark .help-section ul,
    .dark .help-section ol {
        color: rgb(209 213 219);
    }

    .dark .code-example {
        background: rgb(17 24 39);
    }

    .dark .code-example code {
        background: rgb(31 41 55);
        color: rgb(192 132 252);
    }

    .dark .code-example pre {
        background: rgb(17 24 39);
    }

    .dark .api-endpoint {
        background: rgb(31 41 55);
        border-color: rgb(75 85 99);
    }

    .dark .api-endpoint h4 {
        background: rgb(17 24 39);
        color: rgb(248 113 113);
    }

    .dark .api-endpoint p {
        color: rgb(156 163 175);
    }
}

html.dark .help-section {
    background: rgb(31 41 55);
}

html.dark .help-section p,
html.dark .help-section ul,
html.dark .help-section ol {
    color: rgb(209 213 219);
}

html.dark .code-example {
    background: rgb(17 24 39);
}

html.dark .code-example code {
    background: rgb(31 41 55);
    color: rgb(192 132 252);
}

html.dark .code-example pre {
    background: rgb(17 24 39);
}

html.dark .api-endpoint {
    background: rgb(31 41 55);
    border-color: rgb(75 85 99);
}

html.dark .api-endpoint h4 {
    background: rgb(17 24 39);
    color: rgb(248 113 113);
}

html.dark .api-endpoint p {
    color: rgb(156 163 175);
}
</style>
