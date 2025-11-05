<div class="container help-page">
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
.help-page {
    max-width: 900px;
}

.help-section {
    margin-bottom: 3rem;
}

.help-section h3 {
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}

.code-example {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border-left: 4px solid #3498db;
}

.code-example h4 {
    margin-top: 0;
    color: #2c3e50;
}

.code-example code {
    background: #e9ecef;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    font-family: 'Monaco', 'Courier New', monospace;
}

.code-example pre {
    background: #2c3e50;
    color: #ecf0f1;
    padding: 1rem;
    border-radius: 4px;
    overflow-x: auto;
    margin: 1rem 0;
}

.code-example pre code {
    background: none;
    color: inherit;
    padding: 0;
}

.api-reference {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.api-endpoint {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1.5rem;
}

.api-endpoint h4 {
    margin-top: 0;
    color: #e74c3c;
    font-family: 'Monaco', 'Courier New', monospace;
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 4px;
}

.help-section ul {
    list-style-position: inside;
    line-height: 2;
}

.help-section ol {
    line-height: 2;
    padding-left: 1.5rem;
}
</style>
