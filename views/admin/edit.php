<div class="container">
    <h2><?= $command === null ? 'Nouvelle commande' : 'Éditer la commande' ?></h2>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= $command === null ? '/admin/command' : '/admin/command/' . $command->id ?>" id="commandForm">
        <div class="form-group">
            <label for="name">Nom de la commande</label>
            <input
                type="text"
                id="name"
                name="name"
                required
                placeholder="random"
                value="<?= htmlspecialchars($command->name ?? '') ?>"
            >
            <small>Le nom utilisé dans Nightbot (sans le !)</small>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <input
                type="text"
                id="description"
                name="description"
                placeholder="Tire un nombre aléatoire"
                value="<?= htmlspecialchars($command->description ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label for="editor">Code JavaScript</label>
            <div id="editor" style="height: 400px; border: 1px solid #ddd;"></div>
            <textarea id="code" name="code" style="display: none;"><?= htmlspecialchars($command->code ?? '') ?></textarea>
            <small>Variables disponibles : <code>args</code> (array des arguments passés après la commande)</small>
        </div>

        <div class="form-example">
            <strong>Exemple :</strong>
            <pre><code>// args[0] = min, args[1] = max
const min = parseInt(args[0]) || 1;
const max = parseInt(args[1]) || 100;
const random = Math.floor(Math.random() * (max - min + 1)) + min;
return `Nombre aléatoire : ${random}`;
</code></pre>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/admin" class="btn">Annuler</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs/loader.js"></script>
<script>
    require.config({ paths: { vs: 'https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs' }});

    require(['vs/editor/editor.main'], function() {
        const editor = monaco.editor.create(document.getElementById('editor'), {
            value: document.getElementById('code').value,
            language: 'javascript',
            theme: 'vs-dark',
            automaticLayout: true,
            minimap: { enabled: false },
            fontSize: 14,
            lineNumbers: 'on',
            roundedSelection: false,
            scrollBeyondLastLine: false,
        });

        // Sync editor content with hidden textarea before form submit
        document.getElementById('commandForm').addEventListener('submit', function() {
            document.getElementById('code').value = editor.getValue();
        });
    });
</script>
